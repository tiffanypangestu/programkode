<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cluster extends Model
{
    protected $table = 'cluster';

    protected $primaryKey = 'cluster_id';

    protected $fillable = [
        'nama_cluster',
        'deskripsi_cluster',
    ];
}
