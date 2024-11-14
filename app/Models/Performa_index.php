<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Performa_index extends Model
{
    use HasFactory;
    
    protected $table = 'performa_index';
    protected $fillable = [
        'tanggal_perform',
        'emp_id',
        'perform_id',
        'perform_skor',
        'perform_deskripsi',
        'created_by',
        'expired_date'
    ];

    public function detailIndikator()
    {
        return $this->belongsTo(Detail_indikator::class, 'perform_skor', 'detail_id');
    }

    public function detailPerform()
    {
        return $this->hasOne(Ms_reff::class, 'reff_id', 'perform_id');
    }
}
