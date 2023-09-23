<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Employee extends Model
{
    use HasFactory;
    use LogsActivity;

    protected static $logName = 'employee';
    protected static $logFillable = true;
    
    protected $primaryKey = 'emp_id';
    protected $table = 'employee';
    protected $fillable = [
        'emp_id',
        'emp_no',
        'emp_noktp',
        'emp_nokk',
        'emp_name',
        'emp_sex',
        'emp_birthdate',
        'emp_status',
        'emp_npwp',
        'tahun_masuk',
        'unit_kerja',
        'golongan',
        'emp_nip',
        'nomor_rekening',
        'is_medis',
        'ordering_mode',
        'kode_ptkp',
        'kode_golongan',
        'gaji_pokok',
        'emp_active',
        'unit_id_kerja',
        'agama',
        'jabatan_struktural',
        'jabatan_fungsional',
        'created_by',
        'jabatan_type',
        'pendidikan',
        'gaji_add',
        'profesi_id',
        'email',
        'phone',
        'photo',
        'last_risk_index',
        'last_emergency_index',
        'last_position_index'
    ];
}
