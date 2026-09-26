<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel "categories" untuk menyimpan kategori barang.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();                      // Primary key (auto increment)
            $table->string('name')->unique();  // Nama kategori, tidak boleh kembar
            $table->timestamps();              // Kolom created_at & updated_at
        });
    }

    /**
     * Menghapus tabel "categories" saat migration di-rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
