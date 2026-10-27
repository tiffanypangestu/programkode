<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HargaProduk extends Model
{
    protected $table = 'harga_produk';

    protected $primaryKey = 'harga_produk_id';

    protected $fillable = ['produk_id',
        'wilayah_id',
        'harga_produk',
        'tanggal_mulai',
        'tanggal_berakhir'];

    protected function casts(): array
    {
        return [
            'harga_produk' => 'decimal:2',
            'tanggal_mulai' => 'date',
            'tanggal_berakhir' => 'date',
        ];
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id', 'wilayah_id');
    }

    public function transaksiDetail()
    {
        return $this->hasMany(TransaksiDetail::class, 'harga_produk_id', 'harga_produk_id');
    }
}
