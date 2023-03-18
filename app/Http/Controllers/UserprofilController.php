<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Ms_user;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class UserprofilController extends Controller
{
    public $model   = "Employee";
    public $folder  = "user_profil";
    public $route   = "user_profil";

    public $param = [
        'emp_no'   =>  '',
        'emp_noktp'   =>  '',
        'emp_nokk'   =>  '',
        'emp_name'   =>  'required',
        'emp_sex'   =>  '',
        'emp_birthdate'   =>  '',
        'emp_status'   =>  '',
        'emp_npwp'   =>  '',
        'nomor_rekening'   =>  'required',
        'email'   =>  '',
        'phone'   =>  '',
        'photo'   =>  ''
    ];

    public $defaultValue = [
        'emp_id'   =>  '',
        'emp_no'   =>  '',
        'emp_noktp'   =>  '',
        'emp_nokk'   =>  '',
        'emp_name'   =>  '',
        'emp_sex'   =>  '',
        'emp_birthdate'   =>  '',
        'emp_status'   =>  '',
        'emp_npwp'   =>  '',
        'tahun_masuk'   =>  '',
        'unit_kerja'   =>  '',
        'golongan'   =>  '',
        'emp_nip'   =>  '',
        'nomor_rekening'   =>  '',
        'is_medis'   =>  '',
        'ordering_mode'   =>  '',
        'kode_ptkp'   =>  '',
        'kode_golongan'   =>  '',
        'gaji_pokok'   =>  '',
        'emp_active'   =>  't',
        'unit_id_kerja'   =>  '',
        'agama'   =>  '',
        'jabatan_struktural'   =>  '',
        'jabatan_fungsional'   =>  '',
        'jabatan_type'   =>  '',
        'pendidikan'   =>  '',
        'gaji_add'   =>  '',
        'profesi_id'   =>  '',
        'email'   =>  '',
        'phone'   =>  '',
        'photo'   =>  ''
    ];
    public function index()
    {
        $employee = Session::get('sesLogin');
        return $this->themes($this->folder . '.index', compact('employee'), 'Update Profil Pegawai');
    }

    public function update_data(Request $request)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $valid['message']
            ]);
        }
        try {
            //cek username
            $data = Employee::findOrFail($request->emp_id);
            $valid['data']['employee']['emp_birthdate'] = date('Y-m-d',strtotime($request->emp_birthdate));
            if ($request->file('photo')) {
                //hapus old image
                Storage::disk('local')->delete('public/uploads/photo_pegawai/'.$data->photo);
                //upload new image
                $image = $request->file('photo');
                $image->storeAs('public/uploads/photo_pegawai', $image->hashName());
                $valid['data']['employee']['photo'] = $image->hashName();
            }
            $data->update($valid['data']['employee']);
            //update users
            $valid['data']['user']['password_decrypt'] = $valid['data']['user']['password'];
            $valid['data']['user']['password'] = bcrypt($valid['data']['user']['password']);
            $user = Ms_user::where("emp_id",$request->emp_id)->first();
            $user->update($valid['data']['user']);
            $dataEmp = DB::table("employee AS e")
                       ->join("users as us","us.emp_id","=","e.emp_id")
                       ->join("ms_group as mg","mg.group_id","=","us.group_id")
                       ->join("ms_unit as mu","mu.unit_id","=","e.unit_id_kerja")
                       ->leftJoin("detail_indikator as di","di.detail_id","=","e.jabatan_struktural")
                       ->leftJoin("detail_indikator as di2","di2.detail_id","=","e.jabatan_fungsional")
                       ->leftJoin("potongan_statis as ps","ps.pot_stat_code","=","e.kode_ptkp")
                       ->select(["e.*","mg.*","mu.unit_name","di.detail_name AS jabatan_struktural_name","di2.detail_name AS jabatan_fungsional_name","ps.nama_potongan"])
                       ->where("e.emp_id",$request->emp_id)->first();
            Session::put('sesLogin',$dataEmp);
            $resp = [
                'success' => true,
                'message' => 'Data Berhasil Diupdate!'
            ];
        } catch (\Exception $e) {
            $resp = [
                'success' => false,
                'message' => 'Data Gagal Diupdate! <br>' . $e->getMessage()
            ];
        }
        return response()->json($resp);
    }

    private function form_validasi($data)
    {
        $validator = Validator::make($data, $this->param);
        //check if validation fails
        if ($validator->fails()) {
            return [
                "code"      => "201",
                "message"   => implode("<br>",$validator->errors()->all())
            ];
        }
        $paramUser = [
            'name'   =>  'required',
            'email'   =>  'required|unique:users,email,'.Auth::user()->id,
            'password'   =>  'required',
        ];
        $data['name'] = $data['emp_name'];
        $validator = Validator::make($data, $paramUser);
        if ($validator->fails()) {
            return [
                "code"      => "201",
                "message"   => implode("<br>",$validator->errors()->all())
            ];
        }
        //filter
        $filter = array_keys($this->param);
        $input = array_filter(
            $data,
            fn ($key) => in_array($key, $filter),
            ARRAY_FILTER_USE_KEY
        );

        //filter user
        $filter = array_keys($paramUser);
        $inputUser = array_filter(
            $data,
            fn ($key) => in_array($key, $filter),
            ARRAY_FILTER_USE_KEY
        );
        return [
            "code"      => "200",
            "data"      => [
                "employee"  => $input,
                "user"      => $inputUser,
            ]
        ];
    }
}