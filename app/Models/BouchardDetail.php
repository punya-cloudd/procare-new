<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BouchardDetail extends Model
{
    use HasFactory;

    protected $table = 'bouchard_detail';

    protected $fillable = [

        'bouchard_id',

        'jam',

        'm00',
        'm15',
        'm30',
        'm45',

    ];

    protected $casts = [

        'jam' => 'integer',

    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIP
    |--------------------------------------------------------------------------
    */

    public function bouchard()
    {
        return $this->belongsTo(
            Bouchard::class,
            'bouchard_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR
    |--------------------------------------------------------------------------
    */

    public function getTotalIntervalAttribute()
    {
        return count(array_filter([
            $this->m00,
            $this->m15,
            $this->m30,
            $this->m45,
        ]));
    }

    public function getIntervalAttribute()
    {
        return [
            '00-15' => $this->m00,
            '15-30' => $this->m15,
            '30-45' => $this->m30,
            '45-60' => $this->m45,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | MASTER AKTIVITAS BOUCHARD
    |--------------------------------------------------------------------------
    */

    public static function aktivitasList()
    {
        return [

            1 => [
                'label' => 'Kat 1 • 1 MET',
                'energi' => 0.26,
                'items' => [
                    'Tidur',
                    'Berbaring / istirahat di ranjang',
                ]
            ],

            2 => [
                'label' => 'Kat 2 • 1.5 MET',
                'energi' => 0.38,
                'items' => [
                    'Duduk (makan)',
                    'Duduk menulis / mengetik',
                    'Duduk membaca',
                    'Menonton TV / dengar radio',
                    'Duduk di kelas / kuliah',
                    'Mandi (duduk)',
                ]
            ],

            3 => [
                'label' => 'Kat 3 • 1.5 MET',
                'energi' => 0.38,
                'items' => [
                    'Berdiri, aktivitas ringan',
                    'Memasak',
                    'Mencuci badan / bercukur',
                    'Membersihkan debu',
                ]
            ],

            4 => [
                'label' => 'Kat 4 • 2.3 MET',
                'energi' => 0.57,
                'items' => [
                    'Berpakaian',
                    'Mengendarai mobil',
                    'Berjalan santai',
                ]
            ],

            5 => [
                'label' => 'Kat 5 • 3.3 MET',
                'energi' => 0.83,
                'items' => [
                    'Menyapu',
                    'Mengepel',
                    'Berjalan agak cepat',
                    'Pekerjaan laboratorium',
                    'Membereskan ranjang',
                ]
            ],

            6 => [
                'label' => 'Kat 6 • 4 MET',
                'energi' => 1.00,
                'items' => [
                    'Bola voli',
                    'Golf',
                    'Tenis meja',
                    'Bersepeda santai',
                    'Bowling',
                ]
            ],

            7 => [
                'label' => 'Kat 7 • 4.8 MET',
                'energi' => 1.20,
                'items' => [
                    'Mengoperasikan mesin',
                    'Berkebun',
                    'Mengangkat beban',
                    'Menyekop',
                ]
            ],

            8 => [
                'label' => 'Kat 8 • 5.6 MET',
                'energi' => 1.40,
                'items' => [
                    'Bulu tangkis',
                    'Renang',
                    'Jogging',
                    'Jalan cepat',
                    'Tenis',
                    'Senam',
                ]
            ],

            9 => [
                'label' => 'Kat 9 • 7.8 MET',
                'energi' => 1.95,
                'items' => [
                    'Lari',
                    'Basket',
                    'Sepak bola',
                    'Mendaki gunung',
                    'Tinju',
                ]
            ],

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    protected function getKategoriKode($value)
    {
        if (empty($value)) {
            return null;
        }

        $data = explode('-', $value);

        return (int) ($data[0] ?? 0);
    }

    protected function getAktivitasIndex($value)
    {
        if (empty($value)) {
            return null;
        }

        $data = explode('-', $value);

        return isset($data[1])
            ? (int) $data[1]
            : 0;
    }

    /*
    |--------------------------------------------------------------------------
    | ENERGI
    |--------------------------------------------------------------------------
    */

    public function energi($value)
    {
        $kategori = $this->getKategoriKode($value);
        $list = self::aktivitasList();

        return $list[$kategori]['energi'] ?? 0;
    }

    /*
    |--------------------------------------------------------------------------
    | LABEL KATEGORI
    |--------------------------------------------------------------------------
    */

    public function labelKategori($value)
    {
        $kategori = $this->getKategoriKode($value);
        $list = self::aktivitasList();

        return $list[$kategori]['label'] ?? '-';
    }

    /*
    |--------------------------------------------------------------------------
    | NAMA AKTIVITAS
    |--------------------------------------------------------------------------
    */

    public function aktivitas($value)
    {
        if (empty($value)) {
            return '-';
        }

        $kategori = $this->getKategoriKode($value);
        $index = $this->getAktivitasIndex($value);
        $list = self::aktivitasList();

        return $list[$kategori]['items'][$index] ?? '-';
    }
}
