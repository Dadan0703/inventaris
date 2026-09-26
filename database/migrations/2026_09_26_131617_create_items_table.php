<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel "items" untuk menyimpan data master barang.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();                                    // Primary key
            $table->string('code')->unique();                // Kode barang, contoh: BRG-001
            $table->string('name');                          // Nama barang
            $table->foreignId('category_id')                 // Relasi ke tabel categories
                ->constrained()
                ->restrictOnDelete();                        // Kategori tidak bisa dihapus jika masih dipakai
            $table->string('unit');                          // Satuan: unit, pcs, roll, box
            $table->unsignedInteger('stock')->default(0);    // Stok saat ini (berubah lewat transaksi)
            $table->unsignedInteger('min_stock')->default(0); // Batas stok minimum
            $table->text('description')->nullable();         // Keterangan (boleh kosong)
            $table->timestamps();                            // created_at & updated_at
        });
    }

    /**
     * Menghapus tabel "items" saat migration di-rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
