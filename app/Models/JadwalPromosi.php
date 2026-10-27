<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalPromosi extends Model
{
    protected $table = 'jadwal_promosi';

    protected $primaryKey = 'jadwal_promosi_id';

    protected $fillable = [
        'promosi_id',
        'spg_id',
        'bc_id',
        'tanggal_promosi',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_promosi' => 'date',
        ];
    }

    public function promosi()
    {
        return $this->belongsTo(Promosi::class, 'promosi_id', 'promosi_id');
    }

    public function spg()
    {
        return $this->belongsTo(Spg::class, 'spg_id', 'spg_id');
    }

    public function bc()
    {
        return $this->belongsTo(Bc::class, 'bc_id', 'bc_id');
    }
}
