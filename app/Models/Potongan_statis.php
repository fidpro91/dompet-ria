<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Potongan_statis extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'pot_stat_id';
    protected $table = 'potongan_statis';
    protected $fillable = [
        'pot_stat_id',
        'pot_stat_code',
        'nama_potongan',
        'potongan_type',
        'potongan_nominal',
        'pot_status',
        'potongan_note',
        'kategori_potongan'
    ];
}
