<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    // Kolom yang boleh diisi secara massal (mass assignment)
    protected $fillable = ['name'];

    /**
     * Relasi: satu kategori memiliki banyak barang.
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
