<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee_off;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;

class Employee_offController extends Controller
{
    public $model   = "Employee_off";
    public $folder  = "employee_off";
    public $route   = "employee_off";

    public $param = [
        'emp_id'            =>  'required',
        'bulan_skor'        =>  'required',
        'periode'           =>  'required',
        'persentase_skor'   =>  'required',
        'keterangan'        =>  '',
        'user_act'          =>  ''
    ];
    public $defaultValue = [
        'id'            =>  '',
        'emp_id'        =>  '',
        'bulan_skor'    =>  '',
        'periode'       =>  '',
        'persentase_skor'       =>  '',
        'keterangan'    =>  '',
        'user_act'      =>  ''
    ];
    public function index()
    {
        return $this->themes($this->folder . '.index', null, $this);
    }

    public function get_dataTable(Request $request)
    {
        $data = Employee_off::join("employee","employee.emp_id","=","employee_off.emp_id")
                ->join("ms_unit","ms_unit.unit_id","=","employee.unit_id_kerja")
                ->get();

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>", [
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route . ".edit", $data->id),
                "ajax-url"  => route($this->route . '.update', $data->id),
                "data-target"  => "page_employee_off"
            ]);

            $button .= " ". Create::action("<i class=\"fas fa-trash\"></i>", [
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
        $employee_off = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('employee_off'));
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
        $save = Employee_off::create($valid['data']);
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

    public function edit(Employee_off $employee_off)
    {
        return view($this->folder . '.form', compact('employee_off'));
    }
    public function update(Request $request, Employee_off $employee_off)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }

        $data = Employee_off::findOrFail($employee_off->id);
        $data->update($valid['data']);
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diudapte!'
        ]);
    }

    public function destroy($id)
    {
        $data = Employee_off::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diudapte!'
        ]);
    }
}
