<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterMakanan extends Model
{
    protected $table = 'master_makanan';

    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'satuan',
        'gram',
        'kalori',
        'keterangan',
        'aktif'
    ];
}