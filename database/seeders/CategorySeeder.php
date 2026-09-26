<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Mengisi tabel categories dengan kategori awal.
     */
    public function run(): void
    {
        $categories = ['Perangkat Jaringan', 'Kabel & Aksesoris', 'Komputer', 'Peralatan'];

        foreach ($categories as $name) {
            Category::create(['name' => $name]);
        }
    }
}
