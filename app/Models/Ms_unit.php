<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ms_unit extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'unit_id';
    protected $table = 'ms_unit';
    protected $fillable = [
        'unit_id',
        'unit_name',
        'is_active',
        'resiko_infeksi',
        'resiko_admin',
        'emergency_id',
        'ka_unit'
    ];
}
