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
        Schema::create('outlet_performance', function (Blueprint $table) {
            $table->id('performa_id');
            $table->foreignId('outlet_id')->constrained('outlet', 'outlet_id')->restrictOnDelete();
            $table->foreignId('wilayah_id')->constrained('wilayah', 'wilayah_id')->restrictOnDelete();

            $table->string('periode_data');
            $table->decimal('omzet', 15, 2)->default(0);
            $table->unsignedInteger('frekuensi_transaksi')->default(0);
            $table->unsignedInteger('jumlah_produk_terjual')->default(0);
            $table->decimal('growth', 8, 2)->default(0);
            $table->decimal('kontribusi', 8, 2)->default(0);
            $table->timestamps();
            $table->unique(['outlet_id', 'periode_data'], 'outlet_performance_periode_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlet_performance');
    }
};
