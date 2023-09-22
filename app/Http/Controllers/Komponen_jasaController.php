<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Komponen_jasa;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;

class Komponen_jasaController extends Controller
{
    public $model   = "Komponen_jasa";
    public $folder  = "komponen_jasa";
    public $route   = "komponen_jasa";

    public $param = [
        'komponen_kode'   =>  '',
        'komponen_nama'   =>  '',
        'komponen_percentase'   =>  'required',
        'has_detail'   =>  '',
        'komponen_parent'   =>  '',
        'is_vip'   =>  '',
        'has_child'   =>  ''
    ];
    public $defaultValue = [
        'komponen_id'   =>  '',
        'komponen_kode'   =>  '',
        'komponen_nama'   =>  '',
        'komponen_percentase'   =>  '',
        'has_detail'   =>  '',
        'komponen_parent'   =>  '',
        'is_vip'   =>  '',
        'has_child'   =>  ''
    ];
    public function index()
    {
        return $this->themes($this->folder . '.index', null, $this);
    }

    public function get_dataTable(Request $request)
    {
        $data = Komponen_jasa::select([
            'komponen_id',
            'komponen_kode',
            'komponen_nama',
            'komponen_percentase',
            'has_detail',
            'komponen_parent',
            'is_vip',
            'has_child'
        ]);

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>", [
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route . ".edit", $data->komponen_id),
                "ajax-url"  => route($this->route . '.update', $data->komponen_id),
                "data-target"  => "page_komponen_jasa"
            ]);

            $button .= Create::action("<i class=\"fas fa-trash\"></i>", [
                "class"     => "btn btn-danger btn-xs",
                "onclick"   => "delete_row(this)",
                "x-token"   => csrf_token(),
                "data-url"  => route($this->route . ".destroy", $data->komponen_id),
            ]);
            return $button;
        })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function create()
    {
        $komponen_jasa = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('komponen_jasa'));
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
            Komponen_jasa::create($valid['data']);
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

    public function edit(Komponen_jasa $komponen_jasa)
    {
        return view($this->folder . '.form', compact('komponen_jasa'));
    }
    public function update(Request $request, Komponen_jasa $komponen_jasa)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = Komponen_jasa::findOrFail($komponen_jasa->komponen_id);
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
        $data = Komponen_jasa::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }
}
