<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bouchard extends Model
{
    use HasFactory;

    protected $table = 'bouchard';

    protected $fillable = [

        'peserta_id',
        'petugas_id',

        'tanggal',

        'berat_badan',

        'total_kalori',

        'kategori',

        'catatan',

        'created_by',
        'updated_by',

    ];

    protected $casts = [

        'tanggal' => 'date',
        'berat_badan' => 'decimal:2',
        'total_kalori' => 'decimal:2',
        'kategori' => 'string',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIP
    |--------------------------------------------------------------------------
    */

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'petugas_id');
    }

    public function detail()
    {
        return $this->hasMany(
            BouchardDetail::class,
            'bouchard_id'
        );
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR
    |--------------------------------------------------------------------------
    */

    public function getJumlahJamAttribute()
    {
        return $this->detail()->count();
    }
}
