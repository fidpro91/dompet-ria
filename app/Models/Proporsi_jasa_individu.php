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
        $data = DB::table("proporsi_jasa_individu as pi")
                ->select(["id_jaspel",DB::raw("coalesce(pi.no_jaspel,jp.no_jasa) as no_jaspel")])
                ->leftJoin("jasa_pelayanan as jp","jp.jaspel_id","=","pi.id_jaspel")
                ->orderBy("id_jaspel","desc")
                ->groupBy(["id_jaspel","no_jaspel","jp.no_jasa"])->get();
        return $data;
    }
}
