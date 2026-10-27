<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualan';

    protected $primaryKey = 'penjualan_id';

    protected $fillable = ['outlet_id', 'karyawan_id', 'tanggal_penjualan', 'total_harga', 'status_penjualan'];

    protected function casts(): array
    {
        return [
            'tanggal_penjualan' => 'date',
            'total_harga' => 'decimal:2',
        ];
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
        return $this->hasMany(TransaksiDetail::class, 'transaksi_id', 'penjualan_id');
    }
}
