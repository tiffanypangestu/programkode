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
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id('penjualan_id');
            $table->foreignId('outlet_id')->constrained('outlet', 'outlet_id')->restrictOnDelete();
            $table->foreignId('karyawan_id')->constrained('karyawan', 'karyawan_id')->restrictOnDelete();
            $table->date('tanggal_penjualan');
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->string('status_penjualan', 30)->default('selesai');
            $table->timestamps();
            $table->index(['outlet_id', 'tanggal_penjualan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};
