<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Skor_pegawai extends Model
{
    use HasFactory;
    use LogsActivity;

    protected static $logName = 'skor_pegawai';
    protected static $logFillable = true;
    protected static $recordEvents = ['updated', 'deleted'];

    protected $table = 'skor_pegawai';
    protected $fillable = [
        'basic_index',
        'capacity_index',
        'emergency_index',
        'unit_risk_index',
        'admin_risk_index',
        'position_index',
        'competency_index',
        'total_skor',
        'bulan_update',
        'emp_id',
        'is_confirm',
        'confirm_by',
        'created_by',
        'skor_type',
        'skor_koreksi',
        'skor_note',
        'id_komplain'
    ];
}
