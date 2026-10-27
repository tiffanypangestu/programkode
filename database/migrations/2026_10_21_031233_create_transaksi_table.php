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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('transaksi_id');
            $table->foreignId('wilayah_id')->constrained('wilayah', 'wilayah_id')->restrictOnDelete();
            $table->foreignId('outlet_id')->constrained('outlet', 'outlet_id')->restrictOnDelete();
            $table->foreignId('karyawan_id')->constrained('karyawan', 'karyawan_id')->restrictOnDelete();
            $table->string('nomor_transaksi', 50)->nullable();
            $table->date('tanggal');
            $table->decimal('total_transaksi', 15, 2)->default(0);
            $table->string('tipe_transaksi', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
