<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jp_byname_medis extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'jp_medis_id';
    protected $table = 'jp_byname_medis';
    protected $fillable = [
            'jp_medis_id',
            'jaspel_detail_id',
            'kodepegawai',
            'nama_pegawai',
            'skor',
            'nominal_terima',
            'jaspel_id',
            'pencairan_id',
            'emp_id',
            'komponen_id'
        ];
}
