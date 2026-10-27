<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';

    protected $primaryKey = 'transaksi_id';

    protected $fillable = [
        'wilayah_id',
        'outlet_id',
        'karyawan_id',
        'nomor_transaksi',
        'tanggal',
        'total_transaksi',
        'tipe_transaksi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'total_transaksi' => 'decimal:2',
        ];
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id', 'wilayah_id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'outlet_id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'karyawan_id');
    }

    public function transaksiDetail()
    {
        return $this->hasMany(TransaksiDetail::class, 'transaksi_id', 'transaksi_id');
    }
}
