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
        Schema::table('promosi', function (Blueprint $table) {
            if (!Schema::hasColumn('promosi', 'jenis_promosi')) {
                $table->string('jenis_promosi')->nullable()->after('nama_promosi');
            }
            if (!Schema::hasColumn('promosi', 'diskon')) {
                $table->decimal('diskon', 5, 2)->nullable()->after('jenis_promosi');
            }
            if (!Schema::hasColumn('promosi', 'potongan_harga')) {
                $table->decimal('potongan_harga', 15, 2)->nullable()->after('diskon');
            }
            if (!Schema::hasColumn('promosi', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('potongan_harga');
            }
            if (!Schema::hasColumn('promosi', 'outlet_id')) {
                $table->foreignId('outlet_id')->nullable()->after('kategori_produk')->constrained('outlet', 'outlet_id')->nullOnDelete();
            }
            $table->date('periode_mulai')->nullable()->change();
            $table->date('periode_selesai')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promosi', function (Blueprint $table) {
            if (Schema::hasColumn('promosi', 'outlet_id')) {
                $table->dropForeign(['outlet_id']);
                $table->dropColumn('outlet_id');
            }
            $table->dropColumn(array_filter([
                Schema::hasColumn('promosi', 'jenis_promosi') ? 'jenis_promosi' : null,
                Schema::hasColumn('promosi', 'diskon') ? 'diskon' : null,
                Schema::hasColumn('promosi', 'potongan_harga') ? 'potongan_harga' : null,
                Schema::hasColumn('promosi', 'deskripsi') ? 'deskripsi' : null,
            ]));
            $table->date('periode_mulai')->nullable(false)->change();
            $table->date('periode_selesai')->nullable(false)->change();
        });
    }
};
