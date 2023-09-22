<?php

namespace App\Http\Controllers\Mobile;

use App\Models\Diklat;
use App\Models\Jasa_pelayanan;
use App\Models\Pencairan_jasa;
use App\Models\Pencairan_jasa_header;
use App\Models\Tugas_tambahan;
use fidpro\builder\Bootstrap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ProfilController extends MobileController
{
    public $sess;
    public function __construct()
    {
        $this->sess = Session::get('sesLogin');
    }
    
    public function index()
    {
        return view("mobile.info_profil");
    }

    public function load_info($id)
    {
        $this->sess = Session::get('sesLogin');
        if ($id == 1) {
            $data = DB::table('diklat')/* ->where("peserta_id",$this->sess->emp_id) */->select("sertifikat_no","judul_pelatihan","penyelenggara")->get();
        }else {
            $data = DB::table('tugas_tambahan as tt')/* ->where("emp_id",$this->sess->emp_id) */
                    ->join("detail_indikator as i","i.detail_id","=","tt.jabatan_tugas")
                    ->selectRaw("nomor_sk,nama_tugas,i.detail_name as jabatan")
                    ->get();
        }
        return Bootstrap::tableData($data,["class"=>"table table-hover"]);
    }
}