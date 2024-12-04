<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use App\Models\Jasa_pelayanan;
use App\Models\Komplain_skor;
use App\Models\Log_messager;
use App\Models\Ms_unit;
use App\Models\Pencairan_jasa_header;
use App\Models\Skor_pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PDF;

class Verifikasi_skorController extends Controller
{
    public $titlePage = "Form Verfikasi Skor Individu Pegawai";

    public function index()
    {
        $data["titlePage"] = $this->titlePage;
        $login = Session::get("sesLogin");
        $data["unit_kerja"] = Ms_unit::where("ka_unit",$login->emp_id)
                              ->pluck('unit_name')->implode(',');
        return view('verifikasi_skor.index', $data);
    }

    public function get_data($bulan)
    {
        $login = Session::get("sesLogin");
        $skorPegawai    = DB::select("
            SELECT sp.id,e.emp_no,emp_name,mu.unit_name,sp.total_skor,sp.id_komplain,sp.skor_koreksi,eo.keterangan,sp.is_confirm,
            json_arrayagg(
                    json_object('kode',ds.kode_skor, 'skor', ds.skor,'keterangan',ds.detail_skor)
            )detail
            FROM detail_skor_pegawai ds
            JOIN skor_pegawai sp ON ds.skor_id = sp.id
            JOIN employee as e on ds.emp_id = e.emp_id
            JOIN ms_unit mu ON e.unit_id_kerja = mu.unit_id
            left join employee_off eo on eo.bulan_skor = sp.bulan_update and eo.emp_id = e.emp_id
            where 0=0 and mu.ka_unit = '".$login->emp_id."' and sp.bulan_update = '$bulan'
            GROUP BY sp.id,e.emp_no,emp_name,mu.unit_name,ordering_mode,sp.total_skor,sp.id_komplain,sp.skor_koreksi,eo.keterangan,sp.is_confirm
            ORDER BY ordering_mode,mu.unit_name
        ");
        if (!$skorPegawai) {
            $resp = [
                "code"      => 202,
                "message"   => "Data tidak ditemukan"
            ];
        }else{
            $resp = [
                "code"      => 200,
                "message"   => "OK",
                "content"   => view("verifikasi_skor.data_skor",compact('skorPegawai'))->render()
            ];
        }

        return response()->json($resp);
    }

    public function get_keluhan_respon($bulan)
    {
        $login = Session::get("sesLogin");
        $unitKerja = Ms_unit::where("ka_unit", $login->emp_id)->pluck('unit_id')->toArray();
        if (!$unitKerja) {
            return "Unit kerja tidak ditemukan";
        }
        $results = Komplain_skor::whereHas('skorPegawai', function ($query) use($unitKerja,$bulan) {
            $query->where('bulan_update', $bulan)
                  ->whereHas('employee', function ($subQuery) use($unitKerja) {
                      $subQuery->whereIn('unit_id_kerja', $unitKerja);
                  });
        })->get();

        return view("verifikasi_skor.response_verifikator",compact('results'));

    }

    public function konfirmasi_skor($id)
    {
        Skor_pegawai::find($id)->update([
            "is_confirm"    => "t",
            "confirm_by"    => Auth::id()
        ]);

        return response()->json([
            "code"      => 200,
            "message"   => "Skor Berhasil Dikonfirmasi"
        ]);
    }

    public function validasi_otp(Request $request)
    {
        $sess = Session::get('sesLogin');
        //validasi OTP
        $otp = Log_messager::where([
            // "phone_number"  => $sess->phone,
            "otp_verified"  => '1',
            "kode_otp"      => $request->kodeotp
        ])->whereDate("created_at","=",date("Y-m-d"))->first();

        if (!$otp) {
            return response()->json([
                "code"      => 202,
                "message"   => "Kode OTP tidak sesuai"
            ]);
        }
        //update OTP
        Log_messager::find($otp->id)->update([
            "otp_verified"  => 2
        ]);

        return response()->json([
            "code"      => 200,
            "message"   => "OK",
            "redirect"  => url('verifikasi_skor')
        ]);
    }

    public function save_keluhan(Request $request)
    {
        $skor = Skor_pegawai::find($request->id);

        $komplaiSkor = Komplain_skor::create([
            'tanggal'           => date("Y-m-d H:i:s"),
            'id_skor'           => $request->id,
            'employee_id'       => $skor->emp_id,  
            'isi_komplain'          => $request->alasan,
            'status_komplain'       => "1",
            'user_komplain'         => Auth::id(),
        ]);

        $skor->update([
            "id_komplain"   => $komplaiSkor->id_komplain
        ]);

        return response()->json([
            "code"      => 200,
            "message"   => "Keluhan skor pegawai berhasil dikirim"
        ]);
    }
}