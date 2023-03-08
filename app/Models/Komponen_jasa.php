<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class Komponen_jasa extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'komponen_id';
    protected $table = 'komponen_jasa';
    protected $fillable = [
        'komponen_id',
        'komponen_kode',
        'komponen_nama',
        'komponen_percentase',
        'has_detail',
        'komponen_parent',
        'is_vip',
        'has_child'
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('komponen_kode', 'ASC');
        });
    }
}
