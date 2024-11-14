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
        'emp_id',
        'bulan_skor',
        'keterangan',
        'user_act',
        'periode',
        'persentase_skor'
    ];
}
