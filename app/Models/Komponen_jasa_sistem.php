<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komponen_jasa_sistem extends Model
{
    use HasFactory;

    protected $table = 'komponen_jasa_sistem';
    protected $fillable = [
        'kode_komponen',
        'nama_komponen',
        'percentase_jasa',
        'deskripsi_komponen',
        'komponen_active',
        'type_jasa',
        'for_medis'
    ];
}
