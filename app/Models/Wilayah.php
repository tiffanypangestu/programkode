<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    protected $table = 'wilayah';

    protected $primaryKey = 'wilayah_id';

    protected $fillable = ['nama_wilayah', 'deskripsi_wilayah'];

    public function outlet()
    {
        return $this->hasMany(Outlet::class, 'wilayah_id', 'wilayah_id');
    }

    public function hargaProduk()
    {
        return $this->hasMany(HargaProduk::class, 'wilayah_id', 'wilayah_id');
    }
}
