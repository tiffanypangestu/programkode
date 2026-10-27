<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutletPerformance extends Model
{
    protected $table = 'outlet_performance';

    protected $primaryKey = 'performa_id';

    protected $fillable = [
        'outlet_id',
        'wilayah_id',
        'periode_data',
        'omzet',
        'frekuensi_transaksi',
        'jumlah_produk_terjual',
        'growth',
        'kontribusi',
    ];

    protected function casts(): array
    {
        return [
            'omzet' => 'decimal:2',
            'frekuensi_transaksi' => 'integer',
            'jumlah_produk_terjual' => 'integer',
            'growth' => 'decimal:2',
            'kontribusi' => 'decimal:2',
        ];
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'outlet_id');
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id', 'wilayah_id');
    }

    public function hasilCluster()
    {
        return $this->hasMany(HasilCluster::class, 'performa_id', 'performa_id');
    }

    public function detailPerforma()
    {
        return $this->hasMany(DetailPerforma::class, 'performa_id', 'performa_id');
    }
}
