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
        Schema::table('penjualan', function (Blueprint $table) {
            $table->foreignId('bc_karyawan_id')->nullable()->after('karyawan_id')->constrained('karyawan', 'karyawan_id')->nullOnDelete();
            $table->foreignId('spg_karyawan_id')->nullable()->after('bc_karyawan_id')->constrained('karyawan', 'karyawan_id')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualan', function (Blueprint $table) {
            $table->dropForeign(['bc_karyawan_id']);
            $table->dropForeign(['spg_karyawan_id']);
            $table->dropColumn(['bc_karyawan_id', 'spg_karyawan_id']);
        });
    }
};
