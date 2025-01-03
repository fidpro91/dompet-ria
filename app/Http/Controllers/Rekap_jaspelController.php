<?php

namespace App\Http\Controllers;

use App\Models\Jasa_pelayanan;
use App\Models\Pencairan_jasa_header;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Barryvdh\Snappy\Facades\SnappyPdf;

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
        $folderPath = 'public/slip_remun/' . str_replace('/','_',$idHeader->no_pencairan);
        if (!Storage::exists($folderPath)) {
            // Jika belum ada, buat folder baru
            Storage::makeDirectory($folderPath);
        }
        // return view('laporan.v_lap_detail_jaspel',$data);
        $namaPdf = $data["profil"]->emp_no.'.pdf';
        $pdfPath = storage_path('app/'.$folderPath.'/'.$namaPdf);
        if (file_exists($pdfPath)) {
            // Jika sudah ada, langsung buka di browser
            return response()->file($pdfPath);
        } else {
            $dataJasa = $this->get_detail_jasa($emp_id,$idHeader);
            $dataJasa["profil"] = $data["profil"];
            // Jika belum ada, buat PDF baru
            // $pdf = PDF::loadview("laporan.v_lap_detail_jaspel",$dataJasa);
            // Simpan file PDF di storage/app/public/slip
            // $pdf->save($pdfPath);
            // Buka file PDF di browser
            // return response()->file($pdfPath);
            $pdf = SnappyPdf::loadView("laporan.v_lap_detail_jaspel",$dataJasa)->setOptions([
                'encoding'              => 'utf-8',
                'page-size'             => 'Legal',
                'enable-local-file-access' => true
            ]);
            return $pdf->stream('laporan-pegawai.pdf');
            // return view("laporan.v_lap_detail_jaspel",$dataJasa);
        }
        /* $pdf->save($pdfPath);
        // Buka file PDF di browser
        return response()->download($pdfPath);
        // return $pdf->stream(); */
    }

    function get_detail_jasa($emp_id,$idHeader) {
        
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
                                sp.bulan_update as bulan,jm.skor,jm.nominal_terima as nilai_brutto,GROUP_CONCAT(DISTINCT concat(dp.detail_skor,' (',dp.skor,')') SEPARATOR '<br>')as detail
                            ")
                            ->groupBy(["sp.bulan_update","jm.skor","jm.nominal_terima"])
                            ->where([
                                "jp.id_cair"    => $idHeader->id_cair_header,
                                "jm.emp_id"     => $emp_id,
                                "kj.type_jasa"  => 3
                            ])
                            ->get()->toArray();

        /* $data['jasa_by_penjamin'] = DB::select("
            SELECT x.nama_komponen as pelayanan,x.nama_penjamin,x.skor_jasa,(x.skor_jasa/x.skor*x.nominal_terima)jasa_tunai FROM (
                SELECT jm.komponen_id,ks.nama_komponen,concat(mr.reff_name,' BULAN ',rd.bulan_pelayanan) as nama_penjamin,jm.skor,jm.nominal_terima,
                sum(IF(jm.komponen_id = 9,pm.skor,pm.skor/10000))skor_jasa
                FROM jp_byname_medis jm
                JOIN employee e ON e.emp_id = jm.emp_id
                JOIN jasa_pelayanan jp ON jm.jaspel_id = jp.jaspel_id
                JOIN komponen_jasa_sistem ks ON ks.id = jm.komponen_id
                JOIN point_medis pm on pm.jp_medis_id = jm.jp_medis_id and pm.employee_id = e.emp_id
                JOIN repository_download rd on pm.repo_id = rd.id
                join ms_reff as mr on mr.reff_code = pm.penjamin and mr.reffcat_id = 5
                WHERE jm.emp_id = '$emp_id' AND jp.id_cair = $idHeader->id_cair_header
                GROUP BY rd.bulan_pelayanan,jm.komponen_id,ks.nama_komponen,mr.reff_name,jm.skor,jm.nominal_terima
                ORDER BY rd.bulan_pelayanan
            )x"); */
        $data['jasa_by_penjamin'] = DB::select("
                SELECT 
                    nama_komponen AS keterangan,
                    skor AS total_point,
                    nominal_terima AS nominal,
                    JSON_ARRAYAGG(
                        JSON_OBJECT(
                            'klasifikasi_jasa', klasifikasi_jasa,
                            'uraian_tindakan', group_tindakan
                        )
                    ) AS details
                FROM (
                    SELECT 
                        concat(ks.nama_komponen,' (',jp.keterangan,')') nama_komponen,
                        jm.skor,
                        jm.nominal_terima,
                        kj.klasifikasi_jasa,
                        JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'kodedata', dm.visit_id,
                                'tindakan', dm.nama_tindakan,
                                'tarif', dm.tarif_tindakan,
                                'percentase', dm.percentase_jasa,
                                'point', (dm.skor_jasa/10000)
                            )
                        ) AS group_tindakan
                    FROM 
                        jp_byname_medis jm
                        JOIN jasa_pelayanan jp ON jp.jaspel_id = jm.jaspel_id
                        JOIN point_medis pm ON jm.jp_medis_id = pm.jp_medis_id
                        JOIN detail_tindakan_medis dm ON pm.id_tindakan = dm.tindakan_id
                        JOIN klasifikasi_jasa kj ON kj.id_klasifikasi_jasa = dm.id_klasifikasi_jasa
                        JOIN komponen_jasa_sistem ks ON ks.id = jm.komponen_id
                    WHERE 
                        jm.emp_id = '$emp_id' AND jp.id_cair = $idHeader->id_cair_header
                    GROUP BY 
                        jp.keterangan, ks.nama_komponen, jm.skor, jm.nominal_terima, kj.klasifikasi_jasa
                ) AS subquery
                GROUP BY 
                    nama_komponen, skor, nominal_terima;
            ");

        return $data;
    }
}