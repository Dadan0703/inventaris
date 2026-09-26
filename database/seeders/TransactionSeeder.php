<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Mengisi contoh transaksi barang masuk & keluar selama 6 bulan terakhir,
     * sekaligus memperbarui stok barang.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $staff = User::where('email', 'staf@example.com')->first();

        // Transaksi bulan-bulan sebelumnya (tanggal 10 setiap bulan)
        // Format: [jenis, kode barang, berapa bulan lalu, jumlah, supplier / penerima]
        $previousMonths = [
            ['masuk', 'BRG-001', 5, 20, 'PT Citra Network'],
            ['masuk', 'BRG-003', 5, 15, 'PT Sumber Jaringan'],
            ['masuk', 'BRG-007', 5, 10, 'CV Mitra Komputer'],
            ['masuk', 'BRG-002', 4, 10, 'PT Citra Network'],
            ['masuk', 'BRG-004', 4, 1000, 'PT Sumber Jaringan'],
            ['masuk', 'BRG-006', 4, 5, 'CV Alat Teknik'],
            ['keluar', 'BRG-001', 3, 8, 'Kelas MTCNA Batch 10'],
            ['keluar', 'BRG-003', 3, 6, 'Lab Jaringan 1'],
            ['masuk', 'BRG-005', 3, 8, 'PT Citra Network'],
            ['masuk', 'BRG-008', 3, 6, 'CV Alat Teknik'],
            ['keluar', 'BRG-002', 2, 6, 'Kantor Cabang Jakarta'],
            ['keluar', 'BRG-004', 2, 400, 'Lab Jaringan 2'],
            ['keluar', 'BRG-005', 2, 4, 'Kelas Wireless Batch 4'],
            ['keluar', 'BRG-006', 2, 5, 'Kelas CCNA Batch 7'],
            ['masuk', 'BRG-001', 1, 10, 'PT Citra Network'],
            ['keluar', 'BRG-001', 1, 6, 'Kelas MTCNA Batch 11'],
            ['masuk', 'BRG-003', 1, 5, 'PT Sumber Jaringan'],
            ['keluar', 'BRG-003', 1, 3, 'Lab Jaringan 1'],
            ['masuk', 'BRG-004', 1, 500, 'PT Sumber Jaringan'],
            ['keluar', 'BRG-004', 1, 200, 'Lab Jaringan 2'],
            ['keluar', 'BRG-007', 1, 2, 'Tim Instruktur'],
            ['keluar', 'BRG-008', 1, 2, 'Lab Jaringan 1'],
        ];

        foreach ($previousMonths as $row) {
            $date = now()->startOfMonth()->subMonths($row[2])->addDays(9)->toDateString();
            $this->createTransaction($row[0], $row[1], $date, $row[3], $row[4], $admin);
        }

        // Transaksi beberapa hari terakhir
        // Format: [jenis, kode barang, berapa hari lalu, jumlah, supplier / penerima]
        $recentDays = [
            ['masuk', 'BRG-006', 7, 6, 'CV Alat Teknik'],
            ['masuk', 'BRG-002', 6, 4, 'PT Citra Network'],
            ['keluar', 'BRG-007', 6, 2, 'Tim Instruktur'],
            ['keluar', 'BRG-006', 5, 3, 'Kelas CCNA Batch 8'],
            ['masuk', 'BRG-008', 4, 5, 'CV Alat Teknik'],
            ['masuk', 'BRG-001', 3, 12, 'PT Citra Network'],
            ['keluar', 'BRG-005', 3, 4, 'Kelas Wireless Batch 5'],
            ['keluar', 'BRG-002', 2, 2, 'Kantor Cabang Bandung'],
            ['keluar', 'BRG-003', 2, 3, 'Lab Jaringan 1'],
            ['masuk', 'BRG-004', 1, 500, 'PT Sumber Jaringan'],
            ['masuk', 'BRG-007', 1, 6, 'CV Mitra Komputer'],
            ['keluar', 'BRG-004', 1, 200, 'Lab Jaringan 2'],
            ['masuk', 'BRG-003', 0, 10, 'PT Sumber Jaringan'],
            ['keluar', 'BRG-001', 0, 4, 'Kelas MTCNA Batch 12'],
        ];

        foreach ($recentDays as $row) {
            $date = now()->subDays($row[2])->toDateString();
            // Transaksi keluar dicatat oleh staf, transaksi masuk oleh admin
            $user = $row[0] == 'keluar' ? $staff : $admin;
            $this->createTransaction($row[0], $row[1], $date, $row[3], $row[4], $user);
        }
    }

    /**
     * Membuat satu transaksi masuk/keluar lalu memperbarui stok barangnya.
     */
    private function createTransaction(string $type, string $code, string $date, int $quantity, string $party, User $user): void
    {
        $item = Item::where('code', $code)->first();

        if ($type == 'masuk') {
            StockIn::create([
                'transaction_number' => StockIn::generateTransactionNumber($date),
                'date' => $date,
                'item_id' => $item->id,
                'quantity' => $quantity,
                'supplier' => $party,
                'user_id' => $user->id,
            ]);

            $item->increment('stock', $quantity);
        } else {
            StockOut::create([
                'transaction_number' => StockOut::generateTransactionNumber($date),
                'date' => $date,
                'item_id' => $item->id,
                'quantity' => $quantity,
                'recipient' => $party,
                'user_id' => $user->id,
            ]);

            $item->decrement('stock', $quantity);
        }
    }
}
