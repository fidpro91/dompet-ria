<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use App\Models\Jasa_pelayanan;
use App\Models\Komplain_skor;
use App\Models\Ms_unit;
use App\Models\Pencairan_jasa_header;
use App\Models\Skor_pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

class Verifikasi_skorController extends Controller
{
    public function index()
    {
        $data["skorPegawai"]    = DB::select("
            SELECT sp.id,e.emp_no,emp_name,mu.unit_name,sp.total_skor,
            json_arrayagg(
                    json_object('kode',ds.kode_skor, 'skor', ds.skor,'keterangan',ds.detail_skor)
            )detail
            FROM detail_skor_pegawai ds
            JOIN skor_pegawai sp ON ds.skor_id = sp.id
            JOIN employee as e on ds.emp_id = e.emp_id
            JOIN ms_unit mu ON e.unit_id_kerja = mu.unit_id
            where 0=0 
            GROUP BY sp.id,e.emp_no,emp_name,mu.unit_name,ordering_mode,sp.total_skor
            ORDER BY ordering_mode,mu.unit_name
        ");

        return $this->themes('verifikasi_skor.index', $data, "Verifikasi Skor Individu Pegawai");
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

    public function save_keluhan(Request $request)
    {
        $skor = Skor_pegawai::find($request->id);

        Komplain_skor::insert([
            'tanggal'           => date("Y-m-d H:i:s"),
            'id_skor'           => $request->id,
            'employee_id'       => $skor->emp_id,  
            'isi_komplain'          => $request->alasan,
            'status_komplain'       => "1",
            'user_komplain'         => Auth::id(),
        ]);

        return response()->json([
            "code"      => 200,
            "message"   => "Keluhan skor pegawai berhasil dikirim"
        ]);
    }
}