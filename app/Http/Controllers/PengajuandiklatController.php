<?php

namespace App\Http\Controllers;

use App\Libraries\Qontak;
use App\Libraries\Servant;
use Illuminate\Http\Request;
use App\Models\Diklat;
use App\Models\Employee;
use App\Models\Log_messager;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class PengajuandiklatController extends Controller
{
    public $titlePage = "Form Upload Sertifikat Pendidikan & Pelatihan";
    
    public function index()
    {
        return view("diklat.peserta_diklat",[
            "titlePage"     => $this->titlePage
        ]);
    }

    public function find(Request $request)
    {
        $request["phone"]   = '+62'.$request->phone;
        $employee = Employee::from("employee as e")
                    ->join("ms_unit as mu","mu.unit_id","=","e.unit_id_kerja")
                    ->where([
                        "emp_id"            => $request->peserta_id,
                        // "nomor_rekening"    => $request->nomor_rekening,
                    ])->first();

        if (!$employee) {
            $message = '<div class="alert alert-success">
                <strong>Data pegawai tidak sesuai!</strong> Mohon Sesuaikan nama pegawai dengan nomor rekening. <button class="btn btn-primary" onclick="history.go(-1)">Kembali</button>
            </div>';
            return $message;
        }

        //validasi kode OTP
        $logMessager = Log_messager::where([
            "phone_number"  => $request->phone,
            "otp_verified"  => '1',
            "kode_otp"      =>  $request->kode_otp 
        ])->whereDate("created_at","=",date("Y-m-d"))->first();
        if (!$logMessager) {
            $message = '<div class="alert alert-success">
                <strong>Kode OTP Tidak Sesuai!</strong> <button class="btn btn-primary" onclick="history.go(-1)">Kembali</button>
            </div>';
            return $message;
        }

        //update kode OTP
        Log_messager::find($logMessager->id)->update([
            "otp_verified"     => 2
        ]);

        //update data pegawai
        Employee::find($request->peserta_id)->update([
            "phone"     => $request->phone,
            "email"     => $request->email
        ]);

        Session::put("peserta",$employee);
        return redirect("pengajuan_diklat/form_pengajuan");
    }

    public function send_otp(Request $request)
    {
        $request["phone"]   = '+62'.$request->phone;
        $logMessager = Log_messager::where([
            "phone_number"  => $request->phone,
            "message_type"  =>  1 
        ])->whereDate("created_at",date("Y-m-d"))->count();

        if ($logMessager > 3) {
            return response()->json([
                "code"      => 202,
                "message"   => "Pengajuan kode OTP lebih dari 3x. Silahkan coba kembali esok hari"
            ]);
        }
        $code = rand(10000,50000);
        Qontak::sendOTP($request->phone,"Karyawan",$code);
        $resp = [
            "code"      => 200,
            "message"   => "Kode OTP berhasil dikirim, silahkankan cek whatsapp anda"
        ];
        return response()->json($resp);
    }

    public function validasi_capcha(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'capcha_log' => ['required','captcha'],
        ]);

        if ($validator->fails()) {
            $resp = [
                "code"      => "202",
                "message"   => implode('<br>',$validator->errors()->all())
            ];
        }else{
            $resp = [
                "code"      => "200",
                "message"   => "OK"
            ];
        }
        return response()->json($resp);
    }

    public function store(Request $request)
    {
        list($tgl1,$tgl2) = explode('-',$request->tanggal_pelatihan);
        $request['dari_tanggal'] = date('Y-m-d',strtotime($tgl1));
        $request['sampai_tanggal'] = date('Y-m-d',strtotime($tgl2));
        $request['peserta_id']     = Session::get("peserta")->emp_id;
        $data = $request->all();

        $validator = Validator::make($data,[
            'capcha_log'        => ['required','captcha'],
            'dari_tanggal'      =>  'required',
            'sampai_tanggal'    =>  'required',
            'judul_pelatihan'   =>  'required',
            'penyelenggara'     =>  'required',
            'peserta_id'        =>  'required',
            'sertifikat_file'   => 'required|mimes:pdf',
        ],[
            "capcha_log.captcha"        => "Kode captcha tidak sesuai",
            'sertifikat_file.mimes'     => 'File harus berformat PDF',
        ]);

        if ($validator->fails()) {
            $resp = [
                "code"      => "202",
                "message"   => implode('<br>',$validator->errors()->all())
            ];
            return response()->json($resp);
        }

        try {
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
        Session::flash('message', $message);
        return response()->json([
            "code"      => 200,
            "redirect"  => url("pengajuan_diklat/finish")
        ]);
        // return redirect("pengajuan_diklat/finish")->with('message', $message); 
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