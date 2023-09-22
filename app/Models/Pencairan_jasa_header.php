<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pencairan_jasa_header extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'id_cair_header';
    protected $table = 'pencairan_jasa_header';
    protected $fillable = [
        'id_cair_header',
        'no_pencairan',
        'tanggal_cair',
        'total_nominal',
        'user_act',
        'created_at',
        'keterangan',
        'is_published'
    ];
}
