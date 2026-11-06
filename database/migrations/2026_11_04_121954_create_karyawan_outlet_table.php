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
        Schema::create('karyawan_outlet', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan', 'karyawan_id')->cascadeOnDelete();
            $table->foreignId('outlet_id')->constrained('outlet', 'outlet_id')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['karyawan_id', 'outlet_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan_outlet');
    }
};
