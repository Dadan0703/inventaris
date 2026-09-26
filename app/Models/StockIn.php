<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockIn extends Model
{
    // Kolom yang boleh diisi secara massal
    protected $fillable = [
        'transaction_number',
        'date',
        'item_id',
        'quantity',
        'supplier',
        'description',
        'user_id',
    ];

    // Ubah kolom "date" menjadi objek tanggal (Carbon) agar mudah diformat
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Relasi: transaksi ini milik satu barang.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Relasi: transaksi ini dicatat oleh satu user (petugas).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Membuat nomor transaksi otomatis dengan format BM-BulanTanggal-Urutan.
     * Contoh: BM-0926-001 untuk transaksi pertama tanggal 26 September.
     */
    public static function generateTransactionNumber(string $date): string
    {
        // Hitung jumlah transaksi pada tanggal tersebut, lalu tambah 1
        $sequence = self::whereDate('date', $date)->count() + 1;

        // Ambil bulan & tanggal, contoh "0926"
        $monthDay = date('md', strtotime($date));

        // Gabungkan menjadi nomor transaksi, urutan dibuat 3 digit (001, 002, ...)
        return 'BM-'.$monthDay.'-'.str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }
}
