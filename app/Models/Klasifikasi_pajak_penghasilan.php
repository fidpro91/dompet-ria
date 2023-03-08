<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Klasifikasi_pajak_penghasilan extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'range_id';
    protected $table = 'klasifikasi_pajak_penghasilan';
    protected $fillable = [
'range_id',
'nama_range',
'batas_bawah',
'batas_atas',
'percentase_pajak',
'keterangan',
'range_status'
];
}
