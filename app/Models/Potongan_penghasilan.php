<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Potongan_penghasilan extends Model
{
    use HasFactory;

    // public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'potongan_penghasilan';
    protected $fillable = [
        'pajak_no',
        'id_cair_header',
        'kategori_potongan',
        'total_potongan',
        'potongan_method',
        'created_by'
    ];
}
