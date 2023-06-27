<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skor_pegawai extends Model
{
    use HasFactory;
    protected $table = 'skor_pegawai';
    protected $fillable = [
        'basic_index',
        'capacity_index',
        'emergency_index',
        'unit_risk_index',
        'position_index',
        'competency_index',
        'total_skor',
        'bulan_update',
        'emp_id',
        'admin_risk_index',
        'is_confirm',
        'confirm_by',
        'created_by',
        'skor_type'
    ];
}
