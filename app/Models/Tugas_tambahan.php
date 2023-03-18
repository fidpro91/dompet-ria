<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tugas_tambahan extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'tugas_tambahan';
    protected $fillable = [
'id',
'nama_tugas',
'pemberi_tugas',
'nomor_sk',
'emp_id',
'tanggal_awal',
'tanggal_akhir',
'deskripsi_tugas',
'jabatan_tugas',
'created_at',
'updated_at',
'created_by',
'file_sk',
'is_active'
];
}
