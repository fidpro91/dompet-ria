<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Diklat;
use App\Models\Employee;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class PengajuandiklatController extends Controller
{
    public $titlePage = "Form Pengajuan Sertifikat Pendidikan & Pelatihan";
    
    public function index()
    {
        return view("diklat.peserta_diklat",[
            "titlePage"     => $this->titlePage
        ]);
    }

    public function find(Request $request)
    {
        $employee = Employee::from("employee as e")
                    ->join("ms_unit as mu","mu.unit_id","=","e.unit_id_kerja")
                    ->where([
                        "emp_id"            => $request->peserta_id,
                        // "nomor_rekening"    => $request->nomor_rekening,
                    ])->first();
        if (!$employee) {
            return "Data pegawai tidak sesuai";
        }
        Session::put("peserta",$employee);
        return redirect("pengajuan_diklat/form_pengajuan");
    }

    public function store(Request $request)
    {
        list($tgl1,$tgl2) = explode('-',$request->tanggal_pelatihan);
        $request['dari_tanggal'] = date('Y-m-d',strtotime($tgl1));
        $request['sampai_tanggal'] = date('Y-m-d',strtotime($tgl2));
        $request['peserta_id']     = Session::get("peserta")->emp_id;

        try {
            $data = $request->all();
            if ($request->file('sertifikat_file')) {
                $image = $request->file('sertifikat_file');
                $image->storeAs('public/uploads/sertifikat', $image->hashName());
                $data['sertifikat_file'] = $image->hashName();
            }
            Diklat::create($data);
            $message = '<div class="alert alert-success">
                <strong>Pengajuan penambahan sertifikat berhasil!</strong> Anda dapat menambahkan sertifikat lainnya melalui tombol dibawah ini.
            </div>';
        } catch (\Exception $e) {
            $message = '<div class="alert alert-danger">
                <strong>Gagal menambahkan sertifikat!</strong> Silahkan menghubungi admin untuk bantuan lebih lanjut.
            </div>';
        }
        return redirect("pengajuan_diklat/finish")->with('message', $message); 
    }

    public function form_pengajuan(){
        if (empty(session('peserta'))) {
            return redirect('pengajuan_diklat');
        }
        return view("diklat.form_pengajuan",[
            "titlePage"     => $this->titlePage
        ]);
    }

    public function finish(){
        return view("diklat.finish_upload",[
            "titlePage"     => $this->titlePage
        ]);
    }
}