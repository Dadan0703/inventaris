<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel "stock_ins" untuk mencatat transaksi barang masuk.
     */
    public function up(): void
    {
        Schema::create('stock_ins', function (Blueprint $table) {
            $table->id();                                        // Primary key
            $table->string('transaction_number')->unique();      // No. transaksi, contoh: BM-0926-001
            $table->date('date');                                // Tanggal barang diterima
            $table->foreignId('item_id')->constrained()->restrictOnDelete(); // Barang yang masuk
            $table->unsignedInteger('quantity');                 // Jumlah barang masuk
            $table->string('supplier');                          // Nama supplier / pengirim
            $table->text('description')->nullable();             // Keterangan (boleh kosong)
            $table->foreignId('user_id')->constrained();         // Petugas yang mencatat
            $table->timestamps();                                // created_at & updated_at
        });
    }

    /**
     * Menghapus tabel "stock_ins" saat migration di-rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_ins');
    }
};
