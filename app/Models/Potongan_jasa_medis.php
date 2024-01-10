<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Potongan_jasa_medis extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'potongan_id';
    protected $table = 'potongan_jasa_medis';
    protected $fillable = [
        'potongan_id',
        'pencairan_id',
        'potongan_nama',
        'jasa_brutto',
        'penghasilan_pajak',
        'percentase_pajak',
        'potongan_value',
        'medis_id_awal',
        'akumulasi_penghasilan_pajak',
        'master_potongan',
        'kategori_id',
        'header_id'
    ];
}
