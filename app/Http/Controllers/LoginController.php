<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            if (Session::get('sesLogin')->group_type == 1) {
                return redirect('beranda/index');
            }elseif(Session::get('sesLogin')->group_type == 2){
                return redirect('mobile/index');
            }
        }else{
            return view('login.login');
        }
    }

    public function actionlogin(Request $request)
    {
        $data = [
            'email' => $request->input('email_log'),
            'password' => $request->input('password_log'),
            'captcha' => $request->input('capcha_log'),
        ];
        $validator = Validator::make($data,[
            'email' => ['required'],
            'password' => ['required'],
            'captcha' => ['required','captcha'],
        ]);
        if ($validator->fails()) {
            return [
                "code"      => "202",
                "message"   => implode('<br>',$validator->errors()->all())
            ];
        }
        unset($data['captcha']);
        if (Auth::Attempt($data)) {
            $dataEmp = DB::table("employee AS e")
                       ->join("users as us","us.emp_id","=","e.emp_id")
                       ->join("ms_group as mg","mg.group_id","=","us.group_id")
                       ->join("ms_unit as mu","mu.unit_id","=","e.unit_id_kerja")
                       ->leftJoin("detail_indikator as di","di.detail_id","=","e.jabatan_struktural")
                       ->leftJoin("detail_indikator as di2","di2.detail_id","=","e.jabatan_fungsional")
                       ->leftJoin("potongan_statis as ps","ps.pot_stat_code","=","e.kode_ptkp")
                       ->select(["e.*","mg.*","mu.unit_name","di.detail_name AS jabatan_struktural_name","di2.detail_name AS jabatan_fungsional_name","ps.nama_potongan"])
                       ->where("us.email",$data['email'])->first();
            Session::put('sesLogin',$dataEmp);
            $groupMobile = [4,6];
            if (in_array(Auth::user()->group_id,$groupMobile) ) {
                $redirect = "mobile/index";
            }else{
                $redirect = "beranda/index";
            }
            $resp = [
                "code"      => 200,
                "message"   => "ok",
                "redirect"  => $redirect
            ];
        }else{
            $resp = [
                "code"      => 201,
                "message"   => "username / password salah"
            ];
        }

        return response()->json($resp);
    }

    public function actionlogout()
    {
        Auth::logout();
        Session::flush();
        Cache::flush();
        return redirect('/');
    }

    public function reload_capcha()
    {
        return response()->json(['captcha'=> captcha_img()]);
    }
}
