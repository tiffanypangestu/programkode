<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promosi extends Model
{
    protected $table = 'promosi';

    protected $primaryKey = 'promosi_id';

    protected $fillable = ['nama_promosi', 'deskripsi_promosi', 'tanggal_mulai', 'tanggal_selesai', 'kategori_produk'];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function jadwalPromosi()
    {
        return $this->hasMany(JadwalPromosi::class, 'promosi_id', 'promosi_id');
    }
}
