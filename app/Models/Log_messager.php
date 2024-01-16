<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log_messager extends Model
{
    use HasFactory;

    // public $timestamps = false;
    // protected $primaryKey = 'id';
    protected $table = 'log_messager';
    protected $fillable = [
        'param',
        'file_message',
        'message_status',
        'message_type',
        'kode_otp',
        'phone_number',
        'otp_verified'
    ];
}
