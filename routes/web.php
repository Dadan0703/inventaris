<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutController;
use Illuminate\Support\Facades\Route;

// Halaman utama langsung diarahkan ke dashboard (atau ke login jika belum masuk)
Route::redirect('/', '/dashboard');

// Semua route di dalam grup ini hanya bisa diakses setelah login
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Kategori (CRUD): index, create, store, edit, update, destroy
    Route::resource('kategori', CategoryController::class)
        ->except('show')
        ->parameters(['kategori' => 'category'])
        ->names('categories');

    // Data Barang (CRUD): index, create, store, edit, update, destroy
    Route::resource('barang', ItemController::class)
        ->except('show')
        ->parameters(['barang' => 'item'])
        ->names('items');

    // Barang Masuk
    Route::get('/barang-masuk', [StockInController::class, 'index'])->name('stock-ins.index');
    Route::post('/barang-masuk', [StockInController::class, 'store'])->name('stock-ins.store');

    // Barang Keluar
    Route::get('/barang-keluar', [StockOutController::class, 'index'])->name('stock-outs.index');
    Route::post('/barang-keluar', [StockOutController::class, 'store'])->name('stock-outs.store');

    // Laporan
    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan/export', [ReportController::class, 'export'])->name('reports.export');

    // Profil pengguna (bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route login, logout, lupa password, dll (bawaan Breeze)
require __DIR__.'/auth.php';
