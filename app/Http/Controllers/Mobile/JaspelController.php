<?php

namespace App\Http\Controllers\Mobile;

use App\Models\Jasa_pelayanan;
use App\Models\Pencairan_jasa;
use App\Models\Pencairan_jasa_header;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class JaspelController extends MobileController
{
    public $sess;
    public function __construct()
    {
        $this->sess = Session::get('sesLogin');
    }
    
    public function index()
    {
        $data['pencairan'] = Pencairan_jasa_header::where("is_published","1")->paginate(5);
        return view('mobile.jasa_pelayanan',compact('data'));
    }

    public function monitoring_remun()
    {
        return view('mobile.monitoring_remun');
    }

    public function detail($idCair)
    {
        if (is_numeric($idCair)) {
            Session::put('id_cair',$idCair);
        }
        $this->sess = Session::get('sesLogin');
        $data['jasaBrutto'] = Jasa_pelayanan::from("jasa_pelayanan as jp")
                      ->join("jp_byname_medis as jm","jp.jaspel_id","=","jm.jaspel_id")
                      ->join("komponen_jasa_sistem as ks","ks.id","=","jm.komponen_id")
                      ->groupBy(["ks.nama_komponen"])
                      ->selectRaw("ks.nama_komponen,sum(jm.skor) as total_skor,sum(jm.nominal_terima) as total_brutto")
                      ->where([
                        "jp.id_cair"    => $idCair,
                        "jm.emp_id"     => $this->sess->emp_id,
                      ])
                      ->get();

        $data['potonganJasa'] = DB::table("pencairan_jasa as jp")
                                ->join("potongan_jasa_medis as pm","jp.id_cair","=","pm.pencairan_id")
                                ->select(["pm.*"])
                                ->where([
                                    "jp.id_header"    => $idCair,
                                    "jp.emp_id"     => $this->sess->emp_id,
                                ])
                                ->get();
        return view('mobile.jasa_pelayanan_detail',$data);
    }

    public function skoring()
    {
        $idCair = Session::get('id_cair');
        $this->sess = Session::get('sesLogin');
        $data['skoring'] = DB::table("jasa_by_skoring as js")
                            ->join("jasa_pelayanan as jp","jp.jaspel_id","=","js.jaspel_id")
                            ->join("jp_byname_medis as jm","jm.jp_medis_id","=","js.jp_medis_id")
                            ->join("skor_pegawai as sp","sp.id","=","js.skor_id")
                            ->select([
                                "bulan_update as skor_bulan",
                                "jp.keterangan",
                                DB::raw("coalesce(sp.skor_koreksi,sp.total_skor) as skor"),
                                "jm.nominal_terima as nilai_brutto"
                            ])
                            ->where([
                                "jp.id_cair"    => $idCair,
                                "sp.emp_id"     => $this->sess->emp_id,
                            ])->get()
                            ->map(function ($item) {
                                return (array) $item;
                            })
                            ->toArray();
        return view('mobile.components.skoring',$data);
    }
    
    public function point_medis()
    {
        $idCair = Session::get('id_cair');
        $this->sess = Session::get('sesLogin');
        $data['skoring'] = DB::table("jp_byname_medis as jm")
                            ->join("jasa_pelayanan as jp","jp.jaspel_id","=","jm.jaspel_id")
                            ->join("komponen_jasa_sistem as ks","ks.id","=","jm.komponen_id")
                            ->select([
                                DB::raw("concat(ks.nama_komponen,'<br>',jp.keterangan) as keterangan"),
                                DB::raw("jm.skor as point_medis"),
                                "jm.nominal_terima as nilai_brutto"
                            ])
                            ->where("ks.id","!=",8)
                            ->where([
                                "jp.id_cair"    => $idCair,
                                "jm.emp_id"     => $this->sess->emp_id,
                            ])->get()
                            ->map(function ($item) {
                                return (array) $item;
                            })
                            ->toArray();
        // $data['skoring'] = $data['skoring']->toArray();
        return view('mobile.components.point_medis2',$data);
    }

    /* public function point_medis($id)
    {
        $idCair = Session::get('id_cair');
        $this->sess = Session::get('sesLogin');
        $data['pointMedis'] = 
        DB::select("SELECT x.komponen_id,x.periode_awal,x.periode_akhir,
        json_arrayagg(
                json_object('klasifikasi_jasa',x.klasifikasi_jasa, 'skor', x.total_skor)
        )detail
        FROM (
            SELECT jm.komponen_id,rd.periode_awal,rd.periode_akhir,klasifikasi_jasa,sum(skor_jasa)total_skor FROM detail_tindakan_medis dm
            JOIN repository_download rd ON dm.repo_id = rd.id
            JOIN jp_byname_medis jm ON jm.jaspel_id = rd.jaspel_id AND jm.jp_medis_id = dm.jp_medis_id
            JOIN jasa_pelayanan jp ON jp.jaspel_id = rd.jaspel_id AND jp.jaspel_id = jm.jaspel_id
            WHERE jp.id_cair = '$idCair' and jm.komponen_id = $id and jm.emp_id = ".$this->sess->emp_id."
            GROUP BY jm.komponen_id,nama_penjamin,rd.periode_awal,rd.periode_akhir,klasifikasi_jasa
        )x
        GROUP BY x.komponen_id,x.periode_awal,x.periode_akhir");
        return view('mobile.components.point_medis',$data);
    } */
}