<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokOutlet extends Model
{
    use HasFactory;

    protected $table = 'stok_outlet';

    protected $primaryKey = 'stok_outlet_id';

    protected $fillable = [
        'outlet_id',
        'produk_id',
        'stok_masuk',
        'stok_minimum',
    ];

    protected function casts(): array
    {
        return [
            'stok_masuk' => 'integer',
            'stok_minimum' => 'integer',
        ];
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'outlet_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }
}
