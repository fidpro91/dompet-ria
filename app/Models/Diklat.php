<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diklat extends Model
{
    use HasFactory;

    protected $table = 'diklat';
    protected $fillable = [
        'dari_tanggal',
        'sampai_tanggal',
        'judul_pelatihan',
        'penyelenggara',
        'indikator_skor',
        'peserta_id',
        'lokasi_pelatihan',
        'sertifikat_file',
        'created_by',
        'created_at',
        'updated_at'
    ];
}
