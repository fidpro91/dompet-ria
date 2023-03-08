<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ms_user;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;

class UsersController extends Controller
{
    public $model   = "Users";
    public $folder  = "users";
    public $route   = "users";

    public $param = [
        'name'   =>  'required',
        'email'   =>  'required',
        'email_verified_at'   =>  '',
        'password'   =>  'required',
        'remember_token'   =>  '',
        'created_at'   =>  '',
        'updated_at'   =>  '',
        'password_decrypt'   =>  '',
        'emp_id'   =>  '',
        'group_id'   =>  '',
        'user_active'   =>  ''
    ];
    public $defaultValue = [
        'id'   =>  '',
        'name'   =>  '',
        'email'   =>  '',
        'email_verified_at'   =>  '',
        'password'   =>  '',
        'remember_token'   =>  '',
        'created_at'   =>  '',
        'updated_at'   =>  '',
        'password_decrypt'   =>  '',
        'emp_id'   =>  '',
        'group_id'   =>  '',
        'user_active'   =>  ''
    ];
    public function index()
    {
        return $this->themes($this->folder . '.index', null, $this);
    }

    public function get_dataTable(Request $request)
    {
        $data = Ms_user::select([
            'id',
            'name',
            'email',
            'email_verified_at',
            'password',
            'remember_token',
            'created_at',
            'updated_at',
            'password_decrypt',
            'emp_id',
            'group_id',
            'user_active'
        ]);

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>", [
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route . ".edit", $data->id),
                "ajax-url"  => route($this->route . '.update', $data->id),
                "data-target"  => "page_user"
            ]);

            $button .= Create::action("<i class=\"fas fa-trash\"></i>", [
                "class"     => "btn btn-danger btn-xs",
                "onclick"   => "delete_row(this)",
                "x-token"   => csrf_token(),
                "data-url"  => route($this->route . ".destroy", $data->id),
            ]);
            return $button;
        })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function create()
    {
        $users = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('users'));
    }

    public function store(Request $request)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $valid['message']
            ]);
        }
        try {
            $valid['data']['password'] = bcrypt($request->password);
            $valid['data']['password_decrypt'] = ($request->password);
            Ms_user::create($valid['data']);
            $resp = [
                'success' => true,
                'message' => 'Data Berhasil Disimpan!'
            ];
        } catch (\Exception $e) {
            $resp = [
                'success' => false,
                'message' => 'Data Gagal Disimpan! <br>' . $e->getMessage()
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
                "message"   => implode("<br>", $validator->errors()->all())
            ];
        }
        //filter
        $filter = array_keys($this->param);
        $input = array_filter(
            $data,
            fn ($key) => in_array($key, $filter),
            ARRAY_FILTER_USE_KEY
        );
        return [
            "code"      => "200",
            "data"      => $input
        ];
    }

    public function edit(Ms_user $users)
    {
        return view($this->folder . '.form', compact('users'));
    }
    
    public function update(Request $request, Ms_user $users)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = Ms_user::findOrFail($users->id);
            $valid['data']['password'] = bcrypt($request->password);
            $valid['data']['password_decrypt'] = ($request->password);
            $data->update($valid['data']);
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

    public function destroy($id)
    {
        $data = Ms_user::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }
}
