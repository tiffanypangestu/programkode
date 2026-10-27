<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiDetail extends Model
{
    protected $table = 'transaksi_detail';

    protected $primaryKey = 'transaksi_detail_id';

    protected $fillable = ['transaksi_id', 'produk_id', 'harga_produk_id', 'jumlah', 'harga_satuan', 'subtotal'];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'harga_satuan' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id', 'transaksi_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }

    public function hargaProduk()
    {
        return $this->belongsTo(HargaProduk::class, 'harga_produk_id', 'harga_produk_id');
    }
}
