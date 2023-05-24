<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pencairan_jasa extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'id_cair';
    protected $table = 'pencairan_jasa';
    protected $fillable = [
'id_cair',
'no_pencairan',
'tanggal_cair',
'create_by',
'create_date',
'emp_id',
'total_brutto',
'total_potongan',
'total_netto',
'jaspel_id',
'id_header',
'nomor_rekening'
];
}
