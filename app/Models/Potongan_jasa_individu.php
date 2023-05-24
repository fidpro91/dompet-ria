<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Potongan_jasa_individu extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'pot_ind_id';
    protected $table = 'potongan_jasa_individu';
    protected $fillable = [
        'pot_ind_id',
        'kategori_potongan',
        'emp_id',
        'potongan_type',
        'potongan_value',
        'pot_status',
        'pot_note',
        'last_angsuran',
        'max_angsuran'
    ];
}
