<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockOutController extends Controller
{
    /**
     * Menampilkan form barang keluar dan riwayat transaksinya.
     */
    public function index(Request $request)
    {
        // Kata kunci pencarian riwayat (no. transaksi, nama barang, atau penerima)
        $search = $request->input('search');

        // Ambil riwayat barang keluar beserta data barangnya
        $query = StockOut::with('item');

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('transaction_number', 'like', '%'.$search.'%')
                    ->orWhere('recipient', 'like', '%'.$search.'%')
                    ->orWhereHas('item', function ($query) use ($search) {
                        $query->where('name', 'like', '%'.$search.'%');
                    });
            });
        }

        // Urutkan dari tanggal terbaru, 10 data per halaman
        $stockOuts = $query->orderByDesc('date')->orderByDesc('id')->paginate(10)->withQueryString();

        // Daftar barang untuk dropdown pencarian di form.
        // Setiap barang diubah menjadi pasangan value (id) dan label (teks yang tampil).
        $itemOptions = [];
        foreach (Item::orderBy('code')->get() as $item) {
            $itemOptions[] = [
                'value' => $item->id,
                'label' => $item->code.' — '.$item->name.' (stok: '.$item->stock.' '.$item->unit.')',
            ];
        }

        // Perkiraan nomor transaksi berikutnya untuk hari ini (hanya untuk ditampilkan)
        $nextNumber = StockOut::generateTransactionNumber(now()->toDateString());

        return view('stock-outs.index', compact('stockOuts', 'itemOptions', 'nextNumber', 'search'));
    }

    /**
     * Menyimpan transaksi barang keluar dan mengurangi stok barang.
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $validated = $request->validate([
            'date' => 'required|date',
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'recipient' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Ambil data barang yang dipilih
        $item = Item::find($validated['item_id']);

        // Jumlah keluar tidak boleh melebihi stok yang tersedia
        if ($validated['quantity'] > $item->stock) {
            return back()
                ->withErrors(['quantity' => 'Jumlah melebihi stok tersedia ('.$item->stock.' '.$item->unit.').'])
                ->withInput();
        }

        // Lengkapi data yang tidak diisi dari form
        $validated['transaction_number'] = StockOut::generateTransactionNumber($validated['date']);
        $validated['user_id'] = $request->user()->id;

        // Simpan transaksi dan kurangi stok secara bersamaan
        DB::transaction(function () use ($validated, $item) {
            // 1. Simpan transaksi barang keluar
            StockOut::create($validated);

            // 2. Kurangi stok barang sesuai jumlah yang keluar
            $item->decrement('stock', $validated['quantity']);
        });

        return redirect()->route('stock-outs.index')
            ->with('success', 'Transaksi barang keluar berhasil disimpan.');
    }
}
