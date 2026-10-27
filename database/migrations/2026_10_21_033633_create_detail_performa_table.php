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
        Schema::create('detail_performa', function (Blueprint $table) {
            $table->id('detail_performa_id');
            $table->foreignId('performa_id')->constrained('outlet_performance', 'performa_id')->restrictOnDelete();
            $table->foreignId('wilayah_id')->constrained('wilayah', 'wilayah_id')->restrictOnDelete();
            $table->string('periode_performa');
            $table->decimal('omzet_performa', 15, 2)->default(0);
            $table->unsignedInteger('jumlah_produk_terjual')->default(0);
            $table->unsignedInteger('frekuensi_transaksi')->default(0);
            $table->decimal('persentase_pencapaian', 8, 2)->default(0);
            $table->decimal('nilai_performa', 15, 4)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_performa');
    }
};
