<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;

class EmployeeController extends Controller
{
    public $model   = "Employee";
    public $folder  = "employee";
    public $route   = "employee";

    public $param = [
        'emp_no'   =>  '',
        'emp_noktp'   =>  '',
        'emp_nokk'   =>  '',
        'emp_name'   =>  'required',
        'emp_sex'   =>  '',
        'emp_birthdate'   =>  '',
        'emp_status'   =>  '',
        'emp_npwp'   =>  '',
        'tahun_masuk'   =>  '',
        'unit_kerja'   =>  '',
        'golongan'   =>  '',
        'emp_nip'   =>  '',
        'nomor_rekening'   =>  'required',
        'is_medis'   =>  '',
        'ordering_mode'   =>  '',
        'kode_ptkp'   =>  '',
        'kode_golongan'   =>  '',
        'gaji_pokok'   =>  '',
        'emp_active'   =>  '',
        'unit_id_kerja'   =>  'required',
        'agama'   =>  '',
        'jabatan_struktural'   =>  '',
        'jabatan_fungsional'   =>  '',
        'created_at'   =>  '',
        'updated_at'   =>  '',
        'created_by'   =>  '',
        'jabatan_type'   =>  '',
        'pendidikan'   =>  'required'
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
        'created_at'   =>  '',
        'updated_at'   =>  '',
        'created_by'   =>  '',
        'jabatan_type'   =>  '',
        'pendidikan'   =>  '',
        'gaji_add'   =>  ''
    ];
    public function index()
    {
        return $this->themes($this->folder . '.index', null, $this);
    }

    public function get_dataTable(Request $request)
    {
        $data = Employee::from("employee as e")
                ->join("ms_unit as mu","mu.unit_id","=","e.unit_id_kerja")
                ->select([
                    'emp_id',
                    'emp_no',
                    'nomor_rekening',
                    'emp_name',
                    'emp_sex',
                    'mu.unit_name',
                    'e.golongan',
                    'e.emp_active'
                ]);

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>", [
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route . ".edit", $data->emp_id),
                "ajax-url"  => route($this->route . '.update', $data->emp_id),
                "data-target"  => "page_employee"
            ]);
            $button .= Create::action("<i class=\"fas fa-trash\"></i>", [
                "class"     => "btn btn-danger btn-xs",
                "onclick"   => "delete_row(this)",
                "x-token"   => csrf_token(),
                "data-url"  => route($this->route . ".destroy", $data->emp_id),
            ]);
            return $button;
        })->editColumn("emp_sex",function($data){
            if ($data->emp_sex == 'L') {
                $jns = "Laki-laki";
            }else{
                $jns = "Perempuan";
            }
            return $jns;
        })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function create()
    {
        $employee = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('employee'));
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
        print_r($valid);
        die;
        try {
            $valid['data']['emp_birthdate'] = date('Y-m-d',strtotime($request->emp_birthdate));
            $valid['data']['tahun_masuk']   = date('Y-m-d',strtotime($request->tahun_masuk));
            Employee::create($valid['data']);
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
                "message"   => implode("<br>",$validator->errors()->all())
            ];
        }
        //filter
        // $filter = array_keys($this->param);
        $model = new Employee();
        $filter = $model->getFillable();
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

    public function edit(Employee $employee)
    {
        return view($this->folder . '.form', compact('employee'));
    }
    public function update(Request $request, Employee $employee)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $valid['message']
            ]);
        }
        try {
            $data = Employee::findOrFail($employee->emp_id);
            $valid['data']['emp_birthdate'] = date('Y-m-d',strtotime($request->emp_birthdate));
            $valid['data']['tahun_masuk']   = date('Y-m-d',strtotime($request->tahun_masuk));
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
        $data = Employee::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }
}
