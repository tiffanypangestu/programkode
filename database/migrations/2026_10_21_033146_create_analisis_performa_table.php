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
        Schema::create('analisis_performa', function (Blueprint $table) {
            $table->id('analisis_performa_id');
            $table->string('periode_analisis');
            $table->date('tanggal_analisis')->nullable();
            $table->string('metode', 50)->default('K-Means');
            $table->unsignedInteger('jumlah_cluster')->default(3);
            $table->decimal('silhouette_score', 8, 6)->nullable();
            $table->string('status_analisis', 30)->default('selesai');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analisis_performa');
    }
};
