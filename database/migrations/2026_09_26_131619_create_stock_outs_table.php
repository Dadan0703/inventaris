<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel "stock_outs" untuk mencatat transaksi barang keluar.
     */
    public function up(): void
    {
        Schema::create('stock_outs', function (Blueprint $table) {
            $table->id();                                        // Primary key
            $table->string('transaction_number')->unique();      // No. transaksi, contoh: BK-0926-001
            $table->date('date');                                // Tanggal barang dikeluarkan
            $table->foreignId('item_id')->constrained()->restrictOnDelete(); // Barang yang keluar
            $table->unsignedInteger('quantity');                 // Jumlah barang keluar
            $table->string('recipient');                         // Tujuan / penerima barang
            $table->text('description')->nullable();             // Keterangan (boleh kosong)
            $table->foreignId('user_id')->constrained();         // Petugas yang mencatat
            $table->timestamps();                                // created_at & updated_at
        });
    }

    /**
     * Menghapus tabel "stock_outs" saat migration di-rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_outs');
    }
};
