<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee_off extends Model
{
    use HasFactory;

    // public $timestamps = false;
    // protected $primaryKey = 'id';
    protected $table = 'employee_off';
    protected $fillable = [
        'id',
        'emp_id',
        'bulan_jasa_awal',
        'bulan_jasa_akhir',
        'keterangan',
        'user_act'
    ];
}
