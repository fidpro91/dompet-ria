<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detail_skor_pegawai extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'det_skor_id';
    protected $table = 'detail_skor_pegawai';
    protected $fillable = [
        'det_skor_id',
        'skor_id',
        'emp_id',
        'kode_skor',
        'detail_skor',
        'skor'
    ];
}
