<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ms_user extends Model
{
    use HasFactory;
    protected $table = 'users';
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'emp_id',
        'password_decrypt',
        'group_id',
        'user_active'
    ];
}
