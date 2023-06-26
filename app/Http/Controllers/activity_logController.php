<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\activity_log;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;

class activity_logController extends Controller
{
    public $model   = "activity_log";
    public $folder  = "activity_log";
    public $route   = "activity_log";
    
    public $param = [
'log_name'   =>  '',
'description'   =>  'required',
'subject_type'   =>  '',
'subject_id'   =>  '',
'causer_type'   =>  '',
'causer_id'   =>  '',
'properties'   =>  '',
'created_at'   =>  '',
'updated_at'   =>  ''
];
    public $defaultValue = [
'id'   =>  '',
'log_name'   =>  '',
'description'   =>  '',
'subject_type'   =>  '',
'subject_id'   =>  '',
'causer_type'   =>  '',
'causer_id'   =>  '',
'properties'   =>  '',
'created_at'   =>  '',
'updated_at'   =>  ''
];
    public function index()
    {
        return $this->themes($this->folder . '.index',null,$this);
    }

    public function get_dataTable(Request $request)
    {
        $data = activity_log::select([
                'id',
                'log_name',
                'description',
                'subject_type',
                'subject_id',
                'causer_type',
                'causer_id',
                'properties',
                'created_at',
                'updated_at'
            ]
        );

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>",[
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route.".edit",$data->id),
                "ajax-url"  => route($this->route.'.update',$data->id),
                "data-target"  => "page_activity_log"
            ]);
            
            $button .= Create::action("<i class=\"fas fa-trash\"></i>",[
                "class"     => "btn btn-danger btn-xs",
                "onclick"   => "delete_row(this)",
                "x-token"   => csrf_token(),
                "data-url"  => route($this->route.".destroy",$data->id),
            ]);
            return $button;
        })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function create()
    {
        $activity_log = (object)$this->defaultValue;
        return view($this->folder . '.form',compact('activity_log'));
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
            activity_log::create($valid['data']);
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
        return [
            "code"      => "200",
            "data"      => $input
        ];
    }

    public function edit(activity_log $activity_log)
    {
        return view($this->folder . '.form', compact('activity_log'));
    }
    public function update(Request $request, activity_log $activity_log)
    {
        $valid = $this->form_validasi($request->all());
        if($valid['code'] != 200){
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = activity_log::findOrFail($activity_log->id);
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
        $data = activity_log::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }
}
