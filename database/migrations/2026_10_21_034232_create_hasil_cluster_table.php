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
        Schema::create('hasil_cluster', function (Blueprint $table) {
            $table->id('hasil_cluster_id');
            $table->foreignId('performa_id')->constrained('outlet_performance', 'performa_id')->restrictOnDelete();
            $table->foreignId('wilayah_id')->constrained('wilayah', 'wilayah_id')->restrictOnDelete();
            $table->foreignId('cluster_id')->constrained('cluster', 'cluster_id')->restrictOnDelete();
            $table->foreignId('analisis_performa_id')->nullable()->constrained('analisis_performa', 'analisis_performa_id')->restrictOnDelete();
            $table->decimal('nilai_sillhouette', 8, 6)->nullable();
            $table->text('deskripsi_hasil')->nullable();
            $table->timestamps();
            $table->index(['analisis_performa_id', 'cluster_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_cluster');
    }
};
