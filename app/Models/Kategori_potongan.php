<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori_potongan extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'kategori_potongan_id';
    protected $table = 'kategori_potongan';
    protected $fillable = [
        'kategori_potongan_id',
        'nama_kategori',
        'potongan_type',
        'deskripsi_potongan',
        'potongan_active',
        'is_pajak'
    ];
}
