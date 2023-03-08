<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Komponen_jasa_sistem;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;

class Komponen_jasa_sistemController extends Controller
{
    public $model   = "Komponen_jasa_sistem";
    public $folder  = "komponen_jasa_sistem";
    public $route   = "komponen_jasa_sistem";

    public $param = [
        'kode_komponen'   =>  'required',
        'nama_komponen'   =>  'required',
        'percentase_jasa'   =>  'required',
        'deskripsi_komponen'   =>  '',
        'komponen_active'   =>  'required',
        'type_jasa'   =>  'required',
        'for_medis'   =>  'required'
    ];
    public $defaultValue = [
        'id'   =>  '',
        'kode_komponen'   =>  '',
        'nama_komponen'   =>  '',
        'percentase_jasa'   =>  '',
        'deskripsi_komponen'   =>  '',
        'komponen_active'   =>  '',
        'type_jasa'   =>  '',
        'for_medis'   =>  ''
    ];

    public function index()
    {
        return $this->themes($this->folder . '.index', null, $this);
    }

    public function get_dataTable(Request $request)
    {
        $data = Komponen_jasa_sistem::select([
            'id',
            'kode_komponen',
            'nama_komponen',
            'percentase_jasa',
            'deskripsi_komponen',
            'komponen_active'
        ]);

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>", [
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route . ".edit", $data->id),
                "ajax-url"  => route($this->route . '.update', $data->id),
                "data-target"  => "page_komponen_jasa_sistem"
            ]);

            $button .= Create::action("<i class=\"fas fa-trash\"></i>", [
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
        $komponen_jasa_sistem = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('komponen_jasa_sistem'));
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
            Komponen_jasa_sistem::create($valid['data']);
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

    public function edit(Komponen_jasa_sistem $komponen_jasa_sistem)
    {
        return view($this->folder . '.form', compact('komponen_jasa_sistem'));
    }
    public function update(Request $request, Komponen_jasa_sistem $komponen_jasa_sistem)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = Komponen_jasa_sistem::findOrFail($komponen_jasa_sistem->id);
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
        $data = Komponen_jasa_sistem::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }
}
