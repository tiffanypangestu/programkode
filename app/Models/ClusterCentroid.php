<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClusterCentroid extends Model
{
    protected $table = 'cluster_centroid';

    protected $primaryKey = 'centroid_id';

    protected $fillable = [
        'cluster_id',
        'wilayah_id',
        'analisis_performa_id',
        'iterasi',
        'centroid_omzet',
        'centroid_frekuensi',
        'centroid_produk',
        'centroid_growth',
        'centroid_kontribusi',
    ];

    protected function casts(): array
    {
        return [
            'iterasi' => 'integer',
            'centroid_omzet' => 'decimal:6',
            'centroid_frekuensi' => 'decimal:6',
            'centroid_produk' => 'decimal:6',
            'centroid_growth' => 'decimal:6',
            'centroid_kontribusi' => 'decimal:6',
        ];
    }

    public function cluster()
    {
        return $this->belongsTo(Cluster::class, 'cluster_id', 'cluster_id');
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id', 'wilayah_id');
    }

    public function analisisPerforma()
    {
        return $this->belongsTo(AnalisisPerforma::class, 'analisis_performa_id', 'analisis_performa_id');
    }
}
