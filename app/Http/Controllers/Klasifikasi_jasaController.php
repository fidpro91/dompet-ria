<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Klasifikasi_jasa;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;
use Illuminate\Support\Facades\Session;

class Klasifikasi_jasaController extends Controller
{
    public $model   = "Klasifikasi_jasa";
    public $folder  = "klasifikasi_jasa";
    public $route   = "klasifikasi_jasa";

    public $param = [
        'id_klasifikasi_jasa'   =>  'required',
        'klasifikasi_jasa'   =>  '',
        'percentase_eksekutif'   =>  '',
        'percentase_non_eksekutif'   =>  ''
    ];
    public $defaultValue = [
        'id_klasifikasi_jasa'   =>  '',
        'klasifikasi_jasa'   =>  '',
        'percentase_eksekutif'   =>  '',
        'percentase_non_eksekutif'   =>  ''
    ];
    public function index()
    {
        return view($this->folder . '.index');
    }

    public function get_dataTable(Request $request)
    {
        $data = Klasifikasi_jasa::select([
            'id_klasifikasi_jasa',
            'klasifikasi_jasa',
            'percentase_eksekutif',
            'percentase_non_eksekutif'
        ]);

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>", [
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route . ".edit", $data->id_klasifikasi_jasa),
                "ajax-url"  => route($this->route . '.update', $data->id_klasifikasi_jasa),
                "data-target"  => "page_klasifikasi_jasa"
            ]);

            $button .= Create::action("<i class=\"fas fa-trash\"></i>", [
                "class"     => "btn btn-danger btn-xs",
                "onclick"   => "delete_row(this)",
                "x-token"   => csrf_token(),
                "data-url"  => route($this->route . ".destroy", $data->id_klasifikasi_jasa),
            ]);
            return $button;
        })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function create()
    {
        $klasifikasi_jasa = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('klasifikasi_jasa'));
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
            Klasifikasi_jasa::create($valid['data']);
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

    public function edit(Klasifikasi_jasa $klasifikasi_jasa)
    {
        return view($this->folder . '.form', compact('klasifikasi_jasa'));
    }
    public function update(Request $request, Klasifikasi_jasa $klasifikasi_jasa)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = Klasifikasi_jasa::findOrFail($klasifikasi_jasa->id_klasifikasi_jasa);
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
        $data = Klasifikasi_jasa::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }
}
