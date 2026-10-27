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
        Schema::create('outlet', function (Blueprint $table) {
            $table->id('outlet_id');
            $table->foreignId('wilayah_id')->constrained('wilayah', 'wilayah_id')->restrictOnDelete();
            $table->string('kode_outlet')->unique();
            $table->string('nama_outlet');
            $table->string('alamat_outlet')->nullable();
            $table->string('tipe_outlet')->nullable();
            $table->string('status_outlet')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlet');
    }
};
