<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Klasifikasi_jasa extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'id_klasifikasi_jasa';
    protected $table = 'klasifikasi_jasa';
    protected $fillable = [
        'id_klasifikasi_jasa',
        'klasifikasi_jasa',
        'percentase_eksekutif',
        'percentase_non_eksekutif'
    ];
}
