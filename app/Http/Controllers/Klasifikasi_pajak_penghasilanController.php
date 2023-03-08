<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Klasifikasi_pajak_penghasilan;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;

class Klasifikasi_pajak_penghasilanController extends Controller
{
    public $model   = "Klasifikasi_pajak_penghasilan";
    public $folder  = "klasifikasi_pajak_penghasilan";
    public $route   = "klasifikasi_pajak_penghasilan";
    
    public $param = [
'nama_range'   =>  'required',
'batas_bawah'   =>  'required',
'batas_atas'   =>  'required',
'percentase_pajak'   =>  'required',
'keterangan'   =>  '',
'range_status'   =>  ''
];
    public $defaultValue = [
'range_id'   =>  '',
'nama_range'   =>  '',
'batas_bawah'   =>  '',
'batas_atas'   =>  '',
'percentase_pajak'   =>  '',
'keterangan'   =>  '',
'range_status'   =>  ''
];
    public function index()
    {
        return $this->themes($this->folder . '.index',null,$this);
    }

    public function get_dataTable(Request $request)
    {
        $data = Klasifikasi_pajak_penghasilan::select([
'range_id',
'nama_range',
'batas_bawah',
'batas_atas',
'percentase_pajak',
'keterangan',
'range_status'
]);

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>",[
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route.".edit",$data->range_id),
                "ajax-url"  => route($this->route.'.update',$data->range_id),
                "data-target"  => "page_klasifikasi_pajak_penghasilan"
            ]);
            
            $button .= Create::action("<i class=\"fas fa-trash\"></i>",[
                "class"     => "btn btn-danger btn-xs",
                "onclick"   => "delete_row(this)",
                "x-token"   => csrf_token(),
                "data-url"  => route($this->route.".destroy",$data->range_id),
            ]);
            return $button;
        })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function create()
    {
        $klasifikasi_pajak_penghasilan = (object)$this->defaultValue;
        return view($this->folder . '.form',compact('klasifikasi_pajak_penghasilan'));
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
            Klasifikasi_pajak_penghasilan::create($valid['data']);
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

    public function edit(Klasifikasi_pajak_penghasilan $klasifikasi_pajak_penghasilan)
    {
        return view($this->folder . '.form', compact('klasifikasi_pajak_penghasilan'));
    }
    public function update(Request $request, Klasifikasi_pajak_penghasilan $klasifikasi_pajak_penghasilan)
    {
        $valid = $this->form_validasi($request->all());
        if($valid['code'] != 200){
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = Klasifikasi_pajak_penghasilan::findOrFail($klasifikasi_pajak_penghasilan->range_id);
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
        $data = Klasifikasi_pajak_penghasilan::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }
}
