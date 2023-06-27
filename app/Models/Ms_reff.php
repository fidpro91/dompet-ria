<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ms_reff extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'reff_id';
    protected $table = 'ms_reff';
    protected $fillable = [
        'reff_id',
        'reff_code',
        'reff_name',
        'reff_active',
        'reffcat_id'
    ];
}
