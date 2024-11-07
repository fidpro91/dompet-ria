<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rekap_ijin extends Model
{
    use HasFactory;

    // public $timestamps = false;
    // protected $primaryKey = 'id';
    protected $table = 'rekap_ijin';
    protected $fillable = [
        'nip',
        'nama_pegawai',
        'jenis_ijin',
        'tipe_ijin',
        'tgl_mulai',
        'tgl_selesai',
        'lama_ijin',
        'keterangan'
    ];
}
