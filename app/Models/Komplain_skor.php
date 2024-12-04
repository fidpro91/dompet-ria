<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komplain_skor extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'id_komplain';
    protected $table = 'komplain_skor';
    protected $fillable = [
        'id_komplain',
        'tanggal',
        'id_skor',
        'employee_id',
        'isi_komplain',
        'tanggapan_komplain',
        'status_komplain',
        'user_komplain',
        'user_approve'
    ];

    public function skorPegawai() {
        return $this->hasOne(Skor_pegawai::class,"id","id_skor");
    }

    public function employee() {
        return $this->hasOne(Employee::class,"emp_id","employee_id");
    }

    public function userReport() {
        return $this->hasOne(User::class,"id","user_komplain");
    }
}
