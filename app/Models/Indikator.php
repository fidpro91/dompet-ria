<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indikator extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'indikator';
    protected $fillable = [
        'id',
        'kode_indikator',
        'indikator',
        'bobot',
        'group_index',
        'deskripsi',
        'status'
    ];
}
