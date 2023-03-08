<?php

namespace App\Models\Models_builder;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'emp_id';
    protected $table = 'employee';
    protected $fillable = [
        'emp_name', 'emp_sex', 'emp_phone'
    ];

    public function tes_data(){
        return $this->orderBy('emp_name', 'asc')->get();
    }
}
