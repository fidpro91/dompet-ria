<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Proporsi_jasa_individu extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'proporsi_id';
    protected $table = 'proporsi_jasa_individu';
    protected $fillable = [
        'employee_id',
        'komponen_id',
        'jasa_bulan',
        'is_used',
        'id_jaspel'
    ];

    function get_jaspel($filter = null) {
        $data = DB::table("proporsi_jasa_individu")
                ->select(["id_jaspel","no_jaspel"])
                ->orderBy("id_jaspel","desc")
                ->groupBy(["id_jaspel","no_jaspel"])->get();
        return $data;
    }
}
