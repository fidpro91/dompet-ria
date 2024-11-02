<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Table_rekap_absen extends Model
{
    use HasFactory;

    protected $table = 'table_rekap_absen';
    protected $fillable = [
        'nip',
        'bulan_update',
        'tahun_update',
        'nama_pegawai',
        'persentase_kehadiran',
        'keterangan',
    ];

    public function employee() {
      return $this->hasOne(Employee::class,"emp_no","nip");
    }
}
