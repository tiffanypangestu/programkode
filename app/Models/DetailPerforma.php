<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPerforma extends Model
{
    protected $table = 'detail_performa';

    protected $primaryKey = 'detail_performa_id';

    protected $fillable = [
        'performa_id',
        'wilayah_id',
        'periode_performa',
        'omzet_performa',
        'jumlah_produk_terjual',
        'frekuensi_transaksi',
        'persentase_pencapaian',
        'nilai_performa',
    ];

    protected function casts(): array
    {
        return [
            'omzet_performa' => 'decimal:2',
            'jumlah_produk_terjual' => 'integer',
            'frekuensi_transaksi' => 'integer',
            'persentase_pencapaian' => 'decimal:2',
            'nilai_performa' => 'decimal:4',
        ];
    }

    public function outletPerformance()
    {
        return $this->belongsTo(OutletPerformance::class, 'performa_id', 'performa_id');
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id', 'wilayah_id');
    }
}
