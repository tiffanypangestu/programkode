<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalisisPerforma extends Model
{
    protected $table = 'analisis_performa';

    protected $primaryKey = 'analisis_performa_id';

    protected $fillable = [
        'periode_analisis',
        'tanggal_analisis',
        'metode',
        'jumlah_cluster',
        'silhouette_score',
        'status_analisis',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_analisis' => 'date',
            'jumlah_cluster' => 'integer',
            'silhouette_score' => 'decimal:6',
        ];
    }
}
