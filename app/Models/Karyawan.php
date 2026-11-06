<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 'karyawan';

    protected $primaryKey = 'karyawan_id';

    protected $fillable = [
        'user_id',
        'role_id',
        'nama_karyawan',
        'jabatan',
        'nomor_telepon',
        'tahun_masuk',
    ];

    protected function casts(): array
    {
        return [
            'tahun_masuk' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    /**
     * Outlet-outlet tempat karyawan ini bertugas.
     */
    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'karyawan_outlet', 'karyawan_id', 'outlet_id')
                    ->withTimestamps();
    }
}
