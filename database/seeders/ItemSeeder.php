<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Mengisi tabel items dengan contoh barang.
     * Stok dimulai dari 0, lalu diisi oleh TransactionSeeder.
     */
    public function run(): void
    {
        // Format: [kode, nama, kategori, satuan, stok minimum]
        $items = [
            ['BRG-001', 'Router MikroTik hEX RB750Gr3', 'Perangkat Jaringan', 'unit', 10],
            ['BRG-002', 'Switch TP-Link 24 Port', 'Perangkat Jaringan', 'unit', 8],
            ['BRG-003', 'Kabel UTP Cat6 (roll 305 m)', 'Kabel & Aksesoris', 'roll', 5],
            ['BRG-004', 'Konektor RJ45 Cat6', 'Kabel & Aksesoris', 'pcs', 500],
            ['BRG-005', 'Access Point UniFi U6 Lite', 'Perangkat Jaringan', 'unit', 4],
            ['BRG-006', 'Crimping Tool RJ45', 'Peralatan', 'pcs', 5],
            ['BRG-007', 'Laptop Lenovo ThinkPad E14', 'Komputer', 'unit', 6],
            ['BRG-008', 'LAN Tester', 'Peralatan', 'pcs', 4],
        ];

        foreach ($items as $item) {
            Item::create([
                'code' => $item[0],
                'name' => $item[1],
                'category_id' => Category::where('name', $item[2])->first()->id,
                'unit' => $item[3],
                'min_stock' => $item[4],
            ]);
        }
    }
}
