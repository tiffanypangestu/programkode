<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bc extends Model
{
    protected $table = 'bc';

    protected $primaryKey = 'bc_id';

    protected $fillable = [
        'user_id',
        'nama_bc',
        'nomor_telepon',
        'tanggal_masuk',
        'alamat',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function jadwalPromosi()
    {
        return $this->hasMany(JadwalPromosi::class, 'bc_id', 'bc_id');
    }
}
