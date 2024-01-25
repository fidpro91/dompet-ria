<?php

namespace App\Http\Controllers;

use App\Models\Jasa_pelayanan;
use App\Models\Pencairan_jasa_header;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use PDF;

class Rekap_jaspelController extends Controller
{
    public function index()
    {
        return $this->themes('laporan.laporan_rekapjaspel', null, 'Laporan rincian jasa pelayanan');
    }

    public function detail(Request $request)
    {
        $sess = Session::get('sesLogin');
        if (!empty($request->emp_id)) {
            $emp_id = $request->emp_id;
        }else{
            $emp_id = $sess->emp_id;
        }
        $idHeader = Pencairan_jasa_header::whereRaw("DATE_FORMAT(tanggal_cair, '%m-%Y') = '".$request->jaspel_bulan."' and is_published = 1")->latest()->first();
        $data["profil"] = DB::table("pencairan_jasa as pj")
                          ->join("employee as e","e.emp_id","=","pj.emp_id")
                          ->join("pencairan_jasa_header as ph","ph.id_cair_header","=","pj.id_header")
                          ->where([
                            "pj.id_header"    => $idHeader->id_cair_header,
                            "e.emp_id"        => $emp_id,
                          ])->first();
                          
        $data['jasaBrutto'] = Jasa_pelayanan::from("jasa_pelayanan as jp")
                                ->join("jp_byname_medis as jm","jp.jaspel_id","=","jm.jaspel_id")
                                ->join("komponen_jasa_sistem as ks","ks.id","=","jm.komponen_id")
                                ->groupBy(["ks.nama_komponen"])
                                ->selectRaw("ks.nama_komponen,sum(jm.skor) as total_skor,sum(jm.nominal_terima) as total_brutto")
                                ->where([
                                        "jp.id_cair"    => $idHeader->id_cair_header,
                                        "jm.emp_id"        => $emp_id,
                                ])
                                ->get();

        $data['potonganJasa'] = DB::table('potongan_penghasilan as ph')
                                ->join('kategori_potongan as kp', 'kp.kategori_potongan_id', '=', 'ph.kategori_potongan')
                                ->join('pencairan_jasa as pj', 'pj.id_header', '=', 'ph.id_cair_header')
                                ->join('potongan_jasa_medis as pm', function ($join) {
                                    $join->on('pm.pencairan_id', '=', 'pj.id_cair')
                                        ->on('pm.header_id', '=', 'ph.id');
                                })
                                ->select(['kp.nama_kategori', 'pm.*'])
                                ->where([
                                    "ph.id_cair_header"  => $idHeader->id_cair_header,
                                    "pj.emp_id"          => $emp_id,
                                ])
                                ->get();

        $data['skorPegawai'] = DB::table("jp_byname_medis as jm")
                                ->join("jasa_pelayanan as jp","jp.jaspel_id","=","jm.jaspel_id")
                                ->join("komponen_jasa_sistem as kj","kj.id","=","jm.komponen_id")
                                ->leftJoin("jasa_by_skoring as js","js.jp_medis_id","=","jm.jp_medis_id")
                                ->leftJoin("skor_pegawai as sp","sp.id","=","js.skor_id")
                                ->leftJoin("detail_skor_pegawai as dp","dp.skor_id","=","sp.id")
                                ->selectRaw("
                                    sp.bulan_update as bulan,jm.skor,GROUP_CONCAT(DISTINCT concat(dp.detail_skor,' (',dp.skor,')') SEPARATOR '<br>')as detail
                                ")
                                ->groupBy(["sp.bulan_update","jm.skor"])
                                ->where([
                                    "jp.id_cair"    => $idHeader->id_cair_header,
                                    "jm.emp_id"     => $emp_id,
                                    "kj.type_jasa"  => 3
                                ])
                                ->get();
        
        $data['pelayanan'] = DB::table("jp_byname_medis as jm")
                            ->join("jasa_pelayanan as jp","jp.jaspel_id","=","jm.jaspel_id")
                            ->join("point_medis as pm","pm.jp_medis_id","=","jm.jp_medis_id")
                            ->join("detail_tindakan_medis as dm","dm.tindakan_id","=","pm.id_tindakan")
                            ->selectRaw("
                                jm.komponen_id,klasifikasi_jasa,sum(pm.skor) as total_skor,
                                json_arrayagg(
                                    json_object('id_kunjungan',dm.visit_id,'tindakan', dm.nama_tindakan,'skor', dm.skor_jasa)
                                )detail
                            ")
                            ->groupBy(["dm.klasifikasi_jasa","jm.komponen_id"])
                            ->where([
                                "jp.id_cair"    => $idHeader->id_cair_header,
                                "jm.emp_id"     => $emp_id
                            ])
                            ->get();
        $data['jasa_by_penjamin'] = DB::select("
                                    SELECT x.nama_komponen as pelayanan,x.nama_penjamin,x.skor_jasa,(x.skor_jasa/x.skor*x.nominal_terima)jasa_tunai FROM (
                                        SELECT jm.komponen_id,ks.nama_komponen,mr.reff_name as nama_penjamin,jm.skor,jm.nominal_terima,
                                        sum(IF(jm.komponen_id = 9,pm.skor,pm.skor/10000))skor_jasa
                                        FROM jp_byname_medis jm
                                        JOIN employee e ON e.emp_id = jm.emp_id
                                        JOIN jasa_pelayanan jp ON jm.jaspel_id = jp.jaspel_id
                                        JOIN komponen_jasa_sistem ks ON ks.id = jm.komponen_id
                                        JOIN point_medis pm on pm.jp_medis_id = jm.jp_medis_id and pm.employee_id = e.emp_id
                                        join ms_reff as mr on mr.reff_code = pm.penjamin and mr.reffcat_id = 5
                                        WHERE jm.emp_id = '$emp_id' AND jp.id_cair = $idHeader->id_cair_header
                                        GROUP BY jm.komponen_id,ks.nama_komponen,mr.reff_name,jm.skor,jm.nominal_terima
                                        ORDER BY komponen_id
                                    )x");
        $folderPath = 'public/slip_remun/' . str_replace('/','_',$idHeader->no_pencairan);
        if (!Storage::exists($folderPath)) {
            // Jika belum ada, buat folder baru
            Storage::makeDirectory($folderPath);
        }
        // return view('laporan.v_lap_detail_jaspel',$data);
        $namaPdf = $data["profil"]->emp_no.'.pdf';
        $pdfPath = storage_path('app/'.$folderPath.'/'.$namaPdf);
        if (Storage::exists($pdfPath)) {
            // Jika sudah ada, langsung buka di browser
            return response()->file($pdfPath);
        } else {
            // Jika belum ada, buat PDF baru
            $pdf = PDF::loadview("laporan.v_lap_detail_jaspel",$data);
            // Simpan file PDF di storage/app/public/slip
            $pdf->save($pdfPath);
            // Buka file PDF di browser
            return response()->file($pdfPath);
        }
        /* $pdf->save($pdfPath);
        // Buka file PDF di browser
        return response()->download($pdfPath);
        // return $pdf->stream(); */
    }
}