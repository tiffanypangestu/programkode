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
        Schema::create('jadwal_promosi', function (Blueprint $table) {
            $table->id('jadwal_promosi_id');
            $table->foreignId('promosi_id')->constrained('promosi', 'promosi_id')->restrictOnDelete();
            $table->foreignId('spg_id')->nullable()->constrained('spg', 'spg_id')->nullOnDelete();
            $table->foreignId('bc_id')->nullable()->constrained('bc', 'bc_id')->nullOnDelete();
            $table->date('tanggal_promosi');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_promosi');
    }
};
