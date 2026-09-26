<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Mengisi database dengan data awal. Jalankan: php artisan migrate --seed
     */
    public function run(): void
    {
        // Akun untuk login (password keduanya: "password")
        User::factory()->create([
            'name' => 'Admin Gudang',
            'email' => 'admin@example.com',
        ]);

        User::factory()->create([
            'name' => 'Staf Gudang',
            'email' => 'staf@example.com',
        ]);

        // Urutan penting: kategori -> barang -> transaksi
        // $this->call([
        //     CategorySeeder::class,
        //     ItemSeeder::class,
        //     TransactionSeeder::class,
        // ]);
    }
}
