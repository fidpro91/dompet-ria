<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ms_group extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'group_id';
    protected $table = 'ms_group';
    protected $fillable = [
        'group_id',
        'group_code',
        'group_name',
        'group_active',
        'group_type'
    ];
}
