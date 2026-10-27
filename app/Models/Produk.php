<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';

    protected $attributes = [
        'harga_produk' => 0,
    ];

    protected $primaryKey = 'produk_id';

    protected $fillable = ['nama_produk', 'deskripsi_produk', 'kategori_id'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'kategori_id');
    }

    public function hargaProduk()
    {
        return $this->hasMany(HargaProduk::class, 'produk_id', 'produk_id');
    }

    public function transaksiDetail()
    {
        return $this->hasMany(TransaksiDetail::class, 'produk_id', 'produk_id');
    }

    // function untuk menghitung harga rata-rata
    public function getHargaRataRataAttribute()
    {
        return $this->hargaProduk->where('harga_produk', '>', 0)->avg('harga_produk');
    }
}
