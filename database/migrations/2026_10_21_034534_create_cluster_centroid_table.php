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
        Schema::create('cluster_centroid', function (Blueprint $table) {
            $table->id('centroid_id');
            $table->foreignId('cluster_id')->constrained('cluster', 'cluster_id')->cascadeOnDelete();
            $table->foreignId('wilayah_id')->constrained('wilayah', 'wilayah_id')->restrictOnDelete();
            $table->foreignId('analisis_performa_id')->nullable()->constrained('analisis_performa', 'analisis_performa_id')->nullOnDelete();
            $table->unsignedInteger('iterasi')->default(1);
            $table->decimal('centroid_omzet', 15, 6)->default(0);
            $table->decimal('centroid_frekuensi', 15, 6)->default(0);
            $table->decimal('centroid_produk', 15, 6)->default(0);
            $table->decimal('centroid_growth', 15, 6)->default(0);
            $table->decimal('centroid_kontribusi', 15, 6)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cluster_centroid');
    }
};
