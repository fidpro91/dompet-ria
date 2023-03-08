<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ms_unit;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;

class Ms_unitController extends Controller
{
    public $model   = "Ms_unit";
    public $folder  = "ms_unit";
    public $route   = "ms_unit";
    
    public $param = [
'unit_name'   =>  'required',
'is_active'   =>  'required',
'resiko_infeksi'   =>  'required',
'resiko_admin'   =>  'required',
'emergency_id'   =>  'required'
];
    public $defaultValue = [
'unit_id'   =>  '',
'unit_name'   =>  '',
'is_active'   =>  '',
'resiko_infeksi'   =>  '',
'resiko_admin'   =>  '',
'emergency_id'   =>  ''
];
    public function index()
    {
        return $this->themes($this->folder . '.index',null,$this);
    }

    public function get_dataTable(Request $request)
    {
        $data = Ms_unit::select([
'unit_id',
'unit_name',
'is_active',
'resiko_infeksi',
'resiko_admin',
'emergency_id'
]);

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>",[
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route.".edit",$data->unit_id),
                "ajax-url"  => route($this->route.'.update',$data->unit_id),
                "data-target"  => "page_ms_unit"
            ]);
            
            $button .= Create::action("<i class=\"fas fa-trash\"></i>",[
                "class"     => "btn btn-danger btn-xs",
                "onclick"   => "delete_row(this)",
                "x-token"   => csrf_token(),
                "data-url"  => route($this->route.".destroy",$data->unit_id),
            ]);
            return $button;
        })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function create()
    {
        $ms_unit = (object)$this->defaultValue;
        return view($this->folder . '.form',compact('ms_unit'));
    }

    public function store(Request $request)
    {
        $valid = $this->form_validasi($request->all());
        if($valid['code'] != 200){
            return response()->json([
                'success' => false,
                'message' => $valid['message']
            ]);
        }
        try {
            Ms_unit::create($valid['data']);
            $resp = [
                'success' => true,
                'message' => 'Data Berhasil Disimpan!'
            ];
        }catch(\Exception $e){
            $resp = [
                'success' => false,
                'message' => 'Data Gagal Disimpan! <br>'.$e->getMessage()
            ];
        }
        return response()->json($resp);
    }

    private function form_validasi($data){
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

    public function edit(Ms_unit $ms_unit)
    {
        return view($this->folder . '.form', compact('ms_unit'));
    }
    public function update(Request $request, Ms_unit $ms_unit)
    {
        $valid = $this->form_validasi($request->all());
        if($valid['code'] != 200){
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = Ms_unit::findOrFail($ms_unit->unit_id);
            $data->update($valid['data']);
            $resp = [
                'success' => true,
                'message' => 'Data Berhasil Diupdate!'
            ];
        }catch(\Exception $e){
            $resp = [
                'success' => false,
                'message' => 'Data Gagal Diupdate! <br>'.$e->getMessage()
            ];
        }
        return response()->json($resp);
    }

    public function destroy($id)
    {
        $data = Ms_unit::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diudapte!'
        ]);
    }
}
