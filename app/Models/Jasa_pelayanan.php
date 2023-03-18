<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jasa_pelayanan extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'jaspel_id';
    protected $table = 'jasa_pelayanan';
    protected $fillable = [
        'jaspel_id',
        'tanggal_jaspel',
        'periode_jaspel',
        'jaspel_bulan',
        'jaspel_tahun',
        'kodejaminan',
        'namajaminan',
        'nominal_pendapatan',
        'percentase_jaspel',
        'nominal_jaspel',
        'created_by',
        'created_at',
        'status',
        'keterangan',
        'id_cair',
        'no_jasa'
    ];
}
