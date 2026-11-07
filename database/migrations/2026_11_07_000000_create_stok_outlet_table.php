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
        Schema::create('stok_outlet', function (Blueprint $table) {
            $table->id('stok_outlet_id');
            $table->foreignId('outlet_id')->constrained('outlet', 'outlet_id')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk', 'produk_id')->cascadeOnDelete();
            $table->unsignedInteger('stok_masuk')->default(0);
            $table->unsignedInteger('stok_minimum')->default(5);
            $table->timestamps();

            $table->unique(['outlet_id', 'produk_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_outlet');
    }
};
