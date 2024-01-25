<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Point_medis extends Model
{
    use HasFactory;

    // public $timestamps = false;
    // protected $primaryKey = 'id';
    protected $table = 'point_medis';
    protected $fillable = [
        'bulan_jaspel',
        'bulan_pelayanan',
        'id_tindakan',
        'penjamin',
        'skor',
        'is_eksekutif',
        'jaspel_id',
        'jp_medis_id',
        'is_usage',
        'employee_id',
        'repo_id',
        'jenis_tagihan',
        'is_copy'
    ];
}
