<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detail_indikator extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'detail_id';
    protected $table = 'detail_indikator';
    protected $fillable = [
        'detail_id',
        'indikator_id',
        'detail_name',
        'detail_deskripsi',
        'skor',
        'detail_status'
    ];

    public function indikator()
    {
        return $this->belongsTo(Indikator::class, 'indikator_id');
    }
}
