<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spg extends Model
{
    protected $table = 'spg';

    protected $primaryKey = 'spg_id';

    protected $fillable = [
        'user_id',
        'nama_spg',
        'nomor_telepon',
        'tanggal_masuk',
        'alamat'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function jadwalPromosi()
    {
        return $this->hasMany(JadwalPromosi::class, 'spg_id', 'spg_id');
    }
}
