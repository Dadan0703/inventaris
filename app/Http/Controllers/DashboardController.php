<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockOut;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard berisi ringkasan data gudang.
     */
    public function index()
    {
        // ===== 1. Kartu ringkasan =====
        $totalItems = Item::count();
        $totalCategories = Category::count();

        // Total unit masuk & keluar pada bulan ini
        $stockInThisMonth = StockIn::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->sum('quantity');
        $stockOutThisMonth = StockOut::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->sum('quantity');

        // Barang dengan stok di bawah atau sama dengan batas minimum
        $lowStockItems = Item::whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock')
            ->get();

        // ===== 2. Grafik arus barang 6 bulan terakhir =====
        $chart = [];
        for ($i = 5; $i >= 0; $i--) {
            // Mulai dari awal bulan agar pengurangan bulan selalu tepat
            $month = now()->startOfMonth()->subMonths($i);

            $chart[] = [
                'label' => $month->translatedFormat('M'),
                'in' => (int) StockIn::whereYear('date', $month->year)->whereMonth('date', $month->month)->sum('quantity'),
                'out' => (int) StockOut::whereYear('date', $month->year)->whereMonth('date', $month->month)->sum('quantity'),
            ];
        }

        // Nilai tertinggi di grafik, dipakai untuk menghitung tinggi batang (minimal 1 agar tidak dibagi 0)
        $chartMax = max(1, max(array_column($chart, 'in')), max(array_column($chart, 'out')));

        // ===== 3. Persentase stok per kategori =====
        $categories = Category::withSum('items', 'stock')->get();
        $totalStock = $categories->sum('items_sum_stock');

        $categoryShares = [];
        foreach ($categories as $category) {
            $stock = (int) $category->items_sum_stock;

            $categoryShares[] = [
                'name' => $category->name,
                'percent' => $totalStock > 0 ? round($stock / $totalStock * 100) : 0,
            ];
        }

        // Urutkan dari persentase terbesar
        usort($categoryShares, function ($a, $b) {
            return $b['percent'] <=> $a['percent'];
        });

        // ===== 4. Transaksi terbaru (gabungan barang masuk & keluar) =====
        $latestStockIns = StockIn::with('item', 'user')->orderByDesc('date')->orderByDesc('id')->take(5)->get();
        $latestStockOuts = StockOut::with('item', 'user')->orderByDesc('date')->orderByDesc('id')->take(5)->get();

        // Tandai jenis transaksi agar bisa dibedakan di tampilan
        foreach ($latestStockIns as $transaction) {
            $transaction->type = 'Masuk';
        }
        foreach ($latestStockOuts as $transaction) {
            $transaction->type = 'Keluar';
        }

        // Gabungkan keduanya, urutkan dari yang terbaru, ambil 5 teratas
        $latestTransactions = $latestStockIns->concat($latestStockOuts)
            ->sortByDesc('created_at')
            ->sortByDesc('date')
            ->take(5);

        return view('dashboard', compact(
            'totalItems',
            'totalCategories',
            'stockInThisMonth',
            'stockOutThisMonth',
            'lowStockItems',
            'chart',
            'chartMax',
            'categoryShares',
            'latestTransactions',
        ));
    }
}
