<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class activity_log extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'activity_log';
    protected $fillable = [
        'id',
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties',
        'created_at',
        'updated_at'
    ];
}
