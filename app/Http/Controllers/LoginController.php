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
            return redirect('beranda/index');
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
            $data = User::where("email",$data["email"])->first();
            $dataEmp = DB::table("employee")->where("emp_id",$data->emp_id)->first();
            Session::put('sesLogin',$dataEmp);
            $resp = [
                "code"      => 200,
                "message"   => "ok",
                "redirect"  => "beranda/index"
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
