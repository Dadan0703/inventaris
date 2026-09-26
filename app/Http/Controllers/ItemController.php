<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    /**
     * Menampilkan daftar barang, lengkap dengan pencarian & filter kategori.
     */
    public function index(Request $request)
    {
        // Ambil kata kunci pencarian dan filter kategori dari URL (?search=...&category_id=...)
        $search = $request->input('search');
        $categoryId = $request->input('category_id');

        // Mulai query barang, sekalian ambil data kategorinya (eager loading)
        $query = Item::with('category');

        // Jika ada kata kunci, cari berdasarkan kode atau nama barang
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('code', 'like', '%'.$search.'%')
                    ->orWhere('name', 'like', '%'.$search.'%');
            });
        }

        // Jika kategori dipilih, tampilkan barang dari kategori itu saja
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Urutkan berdasarkan kode, tampilkan 10 data per halaman.
        // withQueryString() menjaga filter tetap ada saat pindah halaman.
        $items = $query->orderBy('code')->paginate(10)->withQueryString();

        // Data kategori untuk pilihan filter
        $categories = Category::orderBy('name')->get();

        return view('items.index', compact('items', 'categories', 'search', 'categoryId'));
    }

    /**
     * Menampilkan form tambah barang.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $units = Item::UNITS;

        return view('items.create', compact('categories', 'units'));
    }

    /**
     * Menyimpan barang baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:items,code',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit' => ['required', Rule::in(Item::UNITS)],
            'min_stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        // Simpan data barang (stok awal otomatis 0)
        Item::create($validated);

        // Kembali ke daftar barang dengan pesan sukses
        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit barang.
     */
    public function edit(Item $item)
    {
        $categories = Category::orderBy('name')->get();
        $units = Item::UNITS;

        return view('items.edit', compact('item', 'categories', 'units'));
    }

    /**
     * Menyimpan perubahan data barang.
     */
    public function update(Request $request, Item $item)
    {
        // Validasi input. Kode boleh sama dengan kode barang ini sendiri.
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('items', 'code')->ignore($item->id)],
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit' => ['required', Rule::in(Item::UNITS)],
            'min_stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        // Perbarui data barang
        $item->update($validated);

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    /**
     * Menghapus barang dari database.
     */
    public function destroy(Item $item)
    {
        // Barang yang sudah punya transaksi tidak boleh dihapus agar riwayat tetap utuh
        if ($item->hasTransactions()) {
            return redirect()->route('items.index')
                ->with('error', 'Barang tidak bisa dihapus karena sudah memiliki riwayat transaksi.');
        }

        $item->delete();

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil dihapus.');
    }
}
