<?php

namespace App\Http\Controllers;

use App\Models\Pencairan_jasa_header;
use Illuminate\Http\Request;
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
                ->select(["e.emp_no","e.emp_name","e.golongan","pm.penghasilan_pajak","pm.percentase_pajak","pm.potongan_value"])
                ->where([
                    "kp.is_pajak" => "t",
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
}
