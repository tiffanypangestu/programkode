<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    protected $table = 'outlet';

    protected $primaryKey = 'outlet_id';

    protected $fillable = ['wilayah_id', 'kode_outlet', 'nama_outlet', 'alamat_outlet', 'tipe_outlet', 'status_outlet'];

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id', 'wilayah_id');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'outlet_id', 'outlet_id');
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class, 'outlet_id', 'outlet_id');
    }

    public function OutletPerformance()
    {
        return $this->hasMany(OutletPerformance::class, 'outlet_id', 'outlet_id');
    }

    /**
     * Karyawan yang bertugas di outlet ini.
     */
    public function karyawans()
    {
        return $this->belongsToMany(Karyawan::class, 'karyawan_outlet', 'outlet_id', 'karyawan_id')
                    ->withTimestamps();
    }

    /**
     * Stok produk di outlet ini.
     */
    public function stokOutlet()
    {
        return $this->hasMany(StokOutlet::class, 'outlet_id', 'outlet_id');
    }
}
