<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ms_user;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;

class Ms_userController extends Controller
{
    public $model   = "Ms_user";
    public $folder  = "ms_user";
    public $route   = "ms_user";

    public $param = [
        'user_name'   =>  'required',
        'user_password'   =>  'required',
        'user_salt_encrypt'   =>  'required',
        'user_status'   =>  'required',
        'person_name'   =>  '',
        'user_group'   =>  'required',
        'employee_id'   =>  ''
    ];
    public $defaultValue = [
        'user_name'   =>  '',
        'user_password'   =>  '',
        'user_salt_encrypt'   =>  '',
        'user_id'   =>  '',
        'user_status'   =>  '',
        'person_name'   =>  '',
        'user_group'   =>  '',
        'employee_id'   =>  ''
    ];
    public function index()
    {
        return $this->themes($this->folder . '.index', null, $this);
    }

    public function get_dataTable(Request $request)
    {
        $data = Ms_user::select([
            'name',
            'email',
            'password',
            'emp_id',
            'password_decrypt',
            'group_id',
            'user_active'
        ]);

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>", [
                "class"     => "btn btn-primary btn-sm",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route . ".edit", $data->user_id),
                "ajax-url"  => route($this->route . '.update', $data->user_id),
                "data-target"  => "page_ms_user"
            ]);

            $button .= Create::action("<i class=\"fas fa-trash\"></i>", [
                "class"     => "btn btn-danger btn-sm",
                "onclick"   => "delete_row(this)",
                "x-token"   => csrf_token(),
                "data-url"  => route($this->route . ".destroy", $data->user_id),
            ]);
            return $button;
        })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function create()
    {
        $ms_user = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('ms_user'));
    }

    public function store(Request $request)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        $save = Ms_user::create($valid['data']);
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan!'
        ]);
    }

    private function form_validasi($data)
    {
        $validator = Validator::make($data, $this->param);
        //check if validation fails
        if ($validator->fails()) {
            return [
                "code"      => "201",
                "message"   => $validator->errors()
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

    public function edit(Ms_user $ms_user)
    {
        return view($this->folder . '.form', compact('ms_user'));
    }
    public function update(Request $request, Ms_user $ms_user)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }

        $data = Ms_user::findOrFail($ms_user->user_id);
        $data->update($valid['data']);
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diudapte!'
        ]);
    }

    public function destroy($id)
    {
        $data = Ms_user::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diudapte!'
        ]);
    }
}
