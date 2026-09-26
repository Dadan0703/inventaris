<?php

use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ItemSeeder;
use Database\Seeders\TransactionSeeder;

test('dashboard tampil dengan data contoh', function () {
    // TransactionSeeder membutuhkan dua akun ini
    $admin = User::factory()->create(['email' => 'admin@example.com']);
    User::factory()->create(['email' => 'staf@example.com']);

    $this->seed([CategorySeeder::class, ItemSeeder::class, TransactionSeeder::class]);

    $this->actingAs($admin)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('Total Jenis Barang')
        ->assertSee('Router MikroTik hEX RB750Gr3');
});
