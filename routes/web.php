<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{CategoryController, DashboardController, ItemController, ProfileController, ReportController, StockInController, StockOutController};

Route::redirect('/', '/dashboard');

// 1. BOLEH DIAKSES ADMIN & STAF
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Laporan
    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan/export', [ReportController::class, 'export'])->name('reports.export');

    // Profil (semua boleh edit profil sendiri)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 2. KHUSUS ADMIN DOANG
Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::resource('kategori', CategoryController::class)
        ->except('show')
        ->parameters(['kategori' => 'category'])
        ->names('categories');

    Route::resource('barang', ItemController::class)
        ->except('show')
        ->parameters(['barang' => 'item'])
        ->names('items');

    Route::get('/barang-masuk', [StockInController::class, 'index'])->name('stock-ins.index');
    Route::post('/barang-masuk', [StockInController::class, 'store'])->name('stock-ins.store');

    Route::get('/barang-keluar', [StockOutController::class, 'index'])->name('stock-outs.index');
    Route::post('/barang-keluar', [StockOutController::class, 'store'])->name('stock-outs.store');
});

require __DIR__.'/auth.php';