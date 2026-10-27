<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksi_detail', function (Blueprint $table) {
            $table->id('transaksi_detail_id');
            $table->foreignId('transaksi_id')->constrained('transaksi', 'transaksi_id')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk', 'produk_id')->restrictOnDelete();
            $table->foreignId('harga_produk_id')->constrained('harga_produk', 'harga_produk_id')->restrictOnDelete();
            $table->unsignedInteger('jumlah');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_detail');
    }
};
