<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proporsi_jasa_individu extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'proporsi_id';
    protected $table = 'proporsi_jasa_individu';
    protected $fillable = [
'proporsi_id',
'employee_id',
'komponen_id',
'jasa_bulan',
'is_used',
'id_jaspel'
];
}
