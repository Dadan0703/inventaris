<?php

namespace App\Models;

use Database\Factories\ItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    /** @use HasFactory<ItemFactory> */
    use HasFactory;

    // Daftar satuan yang bisa dipilih di form
    public const UNITS = ['unit', 'pcs', 'roll', 'box'];

    // Kolom yang boleh diisi secara massal.
    // Kolom "stock" sengaja tidak dimasukkan, karena stok hanya berubah lewat transaksi.
    protected $fillable = [
        'code',
        'name',
        'category_id',
        'unit',
        'min_stock',
        'description',
    ];

    /**
     * Relasi: barang ini termasuk dalam satu kategori.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi: barang ini punya banyak riwayat barang masuk.
     */
    public function stockIns(): HasMany
    {
        return $this->hasMany(StockIn::class);
    }

    /**
     * Relasi: barang ini punya banyak riwayat barang keluar.
     */
    public function stockOuts(): HasMany
    {
        return $this->hasMany(StockOut::class);
    }

    /**
     * Menentukan status stok: "Habis", "Menipis", atau "Aman".
     */
    public function status(): string
    {
        // Stok 0 berarti barang habis
        if ($this->stock == 0) {
            return 'Habis';
        }

        // Stok di bawah atau sama dengan batas minimum berarti menipis
        if ($this->stock <= $this->min_stock) {
            return 'Menipis';
        }

        // Selain itu stok masih aman
        return 'Aman';
    }

    /**
     * Mengecek apakah barang sudah pernah dipakai di transaksi.
     * Barang yang sudah punya transaksi tidak boleh dihapus.
     */
    public function hasTransactions(): bool
    {
        return $this->stockIns()->exists() || $this->stockOuts()->exists();
    }
}
