<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilCluster extends Model
{
    protected $table = 'hasil_cluster';

    protected $primaryKey = 'hasil_cluster_id';

    protected $fillable = [
        'performa_id',
        'wilayah_id',
        'cluster_id',
        'analisis_performa_id',
        'nilai_sillhouette',
        'deskripsi_hasil',
    ];

    protected function casts(): array
    {
        return [
            'nilai_sillhouette' => 'decimal:6',
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

    public function cluster()
    {
        return $this->belongsTo(Cluster::class, 'cluster_id', 'cluster_id');
    }

    public function analisisPerforma()
    {
        return $this->belongsTo(AnalisisPerforma::class, 'analisis_performa_id', 'analisis_performa_id');
    }
}
