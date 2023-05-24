<?php

namespace App\Http\Controllers;

use App\Models\Jasa_pelayanan;
use App\Models\Pencairan_jasa_header;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class LaporanController extends Controller
{
    public function index()
    {
        return $this->themes('laporan.index', null, $this);
    }

    public function get_lap_pajak(Request $request)
    {
        $data = Pencairan_jasa_header::from("Pencairan_jasa_header as ph")
                ->join("pencairan_jasa as pj","pj.id_header","=","ph.id_cair_header")
                ->join("employee as e","e.emp_id","=","pj.emp_id")
                ->join("potongan_jasa_medis as pm","pm.pencairan_id","=","pj.id_cair")
                ->join("kategori_potongan as kp","kp.kategori_potongan_id","=","pm.kategori_id")
                ->select(["e.emp_no","e.emp_name","e.golongan","kp.nama_kategori","pm.penghasilan_pajak","pm.percentase_pajak","pm.potongan_value"])
                ->where([
                    "kp.is_pajak"       => $request->is_pajak,
                    "kp.potongan_type"  => 1,
                    "ph.id_cair_header" => $request->id_cair
                ]);
        if ($request->is_medis) {
            $data->where("e.is_medis",$request->is_medis);
        }
        if ($request->emp_status) {
            $data->where("e.emp_status",$request->emp_status);
        }
        $data = $data->get();
        $pdf = PDF::loadview("laporan.v_lap_pajak",compact('data'));
        // return $pdf->download('laporan-pegawai.pdf');
        return $pdf->stream();
    }

    public function get_lap_potongan(Request $request)
    {
        $data = Pencairan_jasa_header::from("Pencairan_jasa_header as ph")
                ->join("pencairan_jasa as pj","pj.id_header","=","ph.id_cair_header")
                ->join("employee as e","e.emp_id","=","pj.emp_id")
                ->join("potongan_jasa_medis as pm","pm.pencairan_id","=","pj.id_cair")
                ->join("potongan_jasa_individu as pi","pi.emp_id","=","pj.emp_id")
                ->join("kategori_potongan as kp","kp.kategori_potongan_id","=","pi.kategori_potongan")
                ->select(["e.emp_no","e.emp_name","kp.nama_kategori","pm.potongan_value","pi.last_angsuran","pi.max_angsuran"])
                ->where([
                    "kp.potongan_type"  => 2,
                    "ph.id_cair_header" => $request->id_cair2
                ]);

        if ($request->kategori_id) {
            $data->where("pm.kategori_id",$request->kategori_id);
        }
        $data = $data->get();
        $pdf = PDF::loadview("laporan.v_lap_potongan",compact('data'));
        // return $pdf->download('laporan-pegawai.pdf');
        return $pdf->stream();
    }
}
