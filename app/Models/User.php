<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'nomor_telepon',
        'nama_lengkap',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function karyawan()
    {
        return $this->hasOne(Karyawan::class, 'user_id', 'user_id');
    }

    public function spg()
    {
        return $this->hasOne(Spg::class, 'user_id', 'user_id');
    }

    public function bc()
    {
        return $this->hasOne(Bc::class, 'user_id', 'user_id');
    }
}
