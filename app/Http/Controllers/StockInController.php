<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockInController extends Controller
{
    /**
     * Menampilkan form barang masuk dan riwayat transaksinya.
     */
    public function index(Request $request)
    {
        // Kata kunci pencarian riwayat (no. transaksi, nama barang, atau supplier)
        $search = $request->input('search');

        // Ambil riwayat barang masuk beserta data barangnya
        $query = StockIn::with('item');

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('transaction_number', 'like', '%'.$search.'%')
                    ->orWhere('supplier', 'like', '%'.$search.'%')
                    ->orWhereHas('item', function ($query) use ($search) {
                        $query->where('name', 'like', '%'.$search.'%');
                    });
            });
        }

        // Urutkan dari tanggal terbaru, 10 data per halaman
        $stockIns = $query->orderByDesc('date')->orderByDesc('id')->paginate(10)->withQueryString();

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
        $nextNumber = StockIn::generateTransactionNumber(now()->toDateString());

        return view('stock-ins.index', compact('stockIns', 'itemOptions', 'nextNumber', 'search'));
    }

    /**
     * Menyimpan transaksi barang masuk dan menambah stok barang.
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $validated = $request->validate([
            'date' => 'required|date',
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'supplier' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Lengkapi data yang tidak diisi dari form
        $validated['transaction_number'] = StockIn::generateTransactionNumber($validated['date']);
        $validated['user_id'] = $request->user()->id;

        // DB::transaction memastikan kedua proses berhasil bersamaan.
        // Jika salah satu gagal, semuanya dibatalkan.
        DB::transaction(function () use ($validated) {
            // 1. Simpan transaksi barang masuk
            StockIn::create($validated);

            // 2. Tambah stok barang sesuai jumlah yang masuk
            Item::find($validated['item_id'])->increment('stock', $validated['quantity']);
        });

        return redirect()->route('stock-ins.index')
            ->with('success', 'Transaksi barang masuk berhasil disimpan.');
    }
}
