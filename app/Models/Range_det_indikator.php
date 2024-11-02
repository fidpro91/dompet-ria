<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Range_det_indikator extends Model
{
    use HasFactory;

    // public $timestamps = false;
    // protected $primaryKey = 'id';
    protected $table = 'range_det_indikator';
    protected $fillable = [
        'det_indikator_id',
        'batas_bawah',
        'batas_atas',
    ];

    function detil_indikator(){
        return $this->hasOne(Detail_indikator::class,"detail_id","det_indikator_id");
    }
}
