<?php

use Illuminate\Support\Facades\{DB, Hash, Route};

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

// 3. BIKIN AKUN ADMIN & STAF - ANTI GAGAL (HAPUS NANTI KALAU UDAH BISA LOGIN)
Route::get('/buat-akun', function () {
    DB::table('users')->updateOrInsert(
        ['email' => 'admin@example.com'],
        ['name' => 'Admin', 'role' => 'admin', 'password' => Hash::make('admin12345'), 'email_verified_at' => now(), 'created_at' => now(), 'updated_at' => now()]
    );
    DB::table('users')->updateOrInsert(
        ['email' => 'staf@example.com'],
        ['name' => 'Staf', 'role' => 'staf', 'password' => Hash::make('staf12345'), 'email_verified_at' => now(), 'created_at' => now(), 'updated_at' => now()]
    );
    return 'OK - akun kebikin, silakan login';
});

Route::get('/cek-akun', function () {
    $users = DB::table('users')->get(['id','name','email','role']);
    return "Jumlah user: ".$users->count()."<br>".json_encode($users);
});

require __DIR__.'/auth.php';