<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pencairan_jasa;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;

class Pencairan_jasaController extends Controller
{
    public $model   = "Pencairan_jasa";
    public $folder  = "pencairan_jasa";
    public $route   = "pencairan_jasa";
    
    public $param = [
'no_pencairan'   =>  '',
'tanggal_cair'   =>  '',
'create_by'   =>  '',
'create_date'   =>  '',
'emp_id'   =>  'required',
'total_brutto'   =>  'required',
'total_potongan'   =>  '',
'total_netto'   =>  '',
'jaspel_id'   =>  '',
'id_header'   =>  '',
'nomor_rekening'   =>  ''
];
    public $defaultValue = [
'id_cair'   =>  '',
'no_pencairan'   =>  '',
'tanggal_cair'   =>  '',
'create_by'   =>  '',
'create_date'   =>  'CURRENT_TIMESTAMP',
'emp_id'   =>  '',
'total_brutto'   =>  '',
'total_potongan'   =>  '',
'total_netto'   =>  '',
'jaspel_id'   =>  '',
'id_header'   =>  '',
'nomor_rekening'   =>  ''
];
    public function index()
    {
        return $this->themes($this->folder . '.index',null,$this);
    }

    public function get_dataTable(Request $request)
    {
        $data = Pencairan_jasa::select([
'id_cair',
'no_pencairan',
'tanggal_cair',
'create_by',
'create_date',
'emp_id',
'total_brutto',
'total_potongan',
'total_netto',
'jaspel_id',
'id_header',
'nomor_rekening'
]);

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>",[
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route.".edit",$data->id_cair),
                "ajax-url"  => route($this->route.'.update',$data->id_cair),
                "data-target"  => "page_pencairan_jasa"
            ]);
            
            $button .= Create::action("<i class=\"fas fa-trash\"></i>",[
                "class"     => "btn btn-danger btn-xs",
                "onclick"   => "delete_row(this)",
                "x-token"   => csrf_token(),
                "data-url"  => route($this->route.".destroy",$data->id_cair),
            ]);
            return $button;
        })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function create()
    {
        $pencairan_jasa = (object)$this->defaultValue;
        return view($this->folder . '.form',compact('pencairan_jasa'));
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
            Pencairan_jasa::create($valid['data']);
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

    public function edit(Pencairan_jasa $pencairan_jasa)
    {
        return view($this->folder . '.form', compact('pencairan_jasa'));
    }
    public function update(Request $request, Pencairan_jasa $pencairan_jasa)
    {
        $valid = $this->form_validasi($request->all());
        if($valid['code'] != 200){
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = Pencairan_jasa::findOrFail($pencairan_jasa->id_cair);
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
        $data = Pencairan_jasa::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }
}
