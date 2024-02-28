<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee_off;
use App\Models\Skor_pegawai;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Employee_offController extends Controller
{
    public $model   = "Employee_off";
    public $folder  = "employee_off";
    public $route   = "employee_off";

    public $param = [
        'emp_id'            =>  'required',
        'keterangan'        =>  '',
        'bulan_skor'        => 'required',
        'user_act'          => 'required',
        'periode'           => 'required',
        'persentase_skor'   => 'required'
    ];

    public function index()
    {
        return $this->themes($this->folder . '.index', null, $this);
    }

    public function get_dataTable(Request $request)
    {
        $data = Employee_off::join("employee","employee.emp_id","=","employee_off.emp_id")
                ->join("ms_unit","ms_unit.unit_id","=","employee.unit_id_kerja")
                ->get([
                    'id',
                    'emp_no',
                    'emp_name',
                    'unit_name',
                    'bulan_skor',
                    'periode',
                    'persentase_skor',
                    'keterangan'
                ]);

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
        $defaultValue =  array_fill_keys(array_keys($this->param), null);
        $defaultValue["id"] = null;
        $employee_off = (object) $defaultValue;
        return view($this->folder . '.form', compact('employee_off'));
    }

    public function store(Request $request)
    {
        $request['user_act'] = Auth::id();
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $valid['message']
            ]);
        }
        
        $hitungSkor = $this->hitung_skor($request);
        if ($hitungSkor["code"] != 200) {
            return response()->json([
                'success' => false,
                'message' => $hitungSkor["message"]
            ]);
        }

        $save = Employee_off::create($valid['data']);
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan!'
        ]);
    }

    public function hitung_skor($request) {
        $dataSkor = Skor_pegawai::where([
            "bulan_update"  => $request->bulan_skor,
            "emp_id"        => $request->emp_id
        ]);

        if (empty($dataSkor->first())) {
            return([
                'code' => 202,
                'message' => "Skor pegawai bulan $request->bulan_skor tidak ditemukan"
            ]);
        }

        //kurangi data persentasi skor
        $valueSkor = $dataSkor->first();
        $totalSkor = $request->persentase_skor/100*$valueSkor->total_skor;
        $dataSkor->update([
            "skor_koreksi"    => $totalSkor
        ]);

        return([
            'code'      => 200,
            'message'   => "OK"
        ]);
    }

    public function update_skor(Request $request) {

        $employee = Employee_off::where("bulan_skor",$request->bulan_skor)->get();
        DB::beginTransaction();
        try {
            foreach ($employee as $key => $value) {
                $dataSkor = Skor_pegawai::where([
                    "bulan_update"  => $value->bulan_skor,
                    "emp_id"        => $value->emp_id
                ]);

                if (empty($dataSkor->first())) {
                    continue;
                }

                //kurangi data persentasi skor
                $valueSkor = $dataSkor->first();
                $totalSkor = $value->persentase_skor/100*$valueSkor->total_skor;
                
                $dataSkor->update([
                    "skor_koreksi"    => $totalSkor
                ]);
            }
            DB::commit();
            $resp = [
                'code'      => 200,
                'message'   => "Skor berhasil diupdate"
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            $resp = [
                'code'      => 201,
                'message'   => "Skor gagal diupdate"
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

    public function edit(Employee_off $employee_off)
    {
        return view($this->folder . '.form', compact('employee_off'));
    }
    public function update(Request $request, Employee_off $employee_off)
    {
        $request['user_act'] = Auth::id();
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $valid['message']
            ]);
        }

        $hitungSkor = $this->hitung_skor($request);
        if ($hitungSkor["code"] != 200) {
            return response()->json([
                'success' => false,
                'message' => $hitungSkor["message"]
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
