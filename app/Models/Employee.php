<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Laravel\Scout\Searchable;

class Employee extends Model
{
    use HasFactory, LogsActivity, Searchable;

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

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'emp_id' => $this->emp_id,
            'emp_no' => $this->emp_no,
            'emp_noktp' => $this->emp_noktp,
            'emp_nokk' => $this->emp_nokk,
            'emp_name' => $this->emp_name,
            'emp_sex' => $this->emp_sex,
            'emp_birthdate' => $this->emp_birthdate,
            'emp_status' => $this->emp_status,
            'emp_npwp' => $this->emp_npwp,
            'tahun_masuk' => $this->tahun_masuk,
            'unit_kerja' => $this->unit_kerja,
            'golongan' => $this->golongan,
            'emp_nip' => $this->emp_nip,
            'nomor_rekening' => $this->nomor_rekening,
            'is_medis' => $this->is_medis,
            'ordering_mode' => $this->ordering_mode,
            'kode_ptkp' => $this->kode_ptkp,
            'kode_golongan' => $this->kode_golongan,
            'gaji_pokok' => $this->gaji_pokok,
            'emp_active' => $this->emp_active,
            'unit_id_kerja' => $this->unit_id_kerja,
            'agama' => $this->agama,
            'jabatan_struktural' => $this->jabatan_struktural,
            'jabatan_fungsional' => $this->jabatan_fungsional,
            'created_by' => $this->created_by,
            'jabatan_type' => $this->jabatan_type,
            'pendidikan' => $this->pendidikan,
            'gaji_add' => $this->gaji_add,
            'profesi_id' => $this->profesi_id,
            'email' => $this->email,
            'phone' => $this->phone,
            'photo' => $this->photo,
            'last_risk_index' => $this->last_risk_index,
            'last_emergency_index' => $this->last_emergency_index,
            'last_position_index' => $this->last_position_index
        ];
    }

    public function unit()
    {
        return $this->belongsTo(Ms_unit::class, 'unit_id_kerja','unit_id');
    }

    public function pendidikanDetail()
    {
        return $this->belongsTo(Detail_indikator::class, 'pendidikan','detail_id');
    }

    public function tugasTambahan()
    {
        return $this->hasMany(Tugas_tambahan::class, 'emp_id', 'emp_id');
    }

    public function performaIndex()
    {
        return $this->hasMany(Performa_index::class, 'emp_id');
    }

    public function diklat()
    {
        return $this->hasMany(DIklat::class,'peserta_id');
    }
}