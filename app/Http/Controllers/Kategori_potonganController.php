<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori_potongan;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;

class Kategori_potonganController extends Controller
{
    public $model   = "Kategori_potongan";
    public $folder  = "kategori_potongan";
    public $route   = "kategori_potongan";

    public $param = [
        'nama_kategori'   =>  'required',
        'potongan_type'   =>  'required',
        'deskripsi_potongan'   =>  '',
        'potongan_active'   =>  'required',
        'is_pajak'   =>  'required'
    ];
    public $defaultValue = [
        'kategori_potongan_id'   =>  '',
        'nama_kategori'   =>  '',
        'potongan_type'   =>  '',
        'deskripsi_potongan'   =>  '',
        'potongan_active'   =>  '',
        'is_pajak'   =>  ''
    ];
    public function index()
    {
        return $this->themes($this->folder . '.index', null, $this);
    }

    public function get_dataTable(Request $request)
    {
        $data = Kategori_potongan::select([
            'kategori_potongan_id',
            'nama_kategori',
            'potongan_type',
            'deskripsi_potongan',
            'potongan_active'
        ]);

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>", [
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route . ".edit", $data->kategori_potongan_id),
                "ajax-url"  => route($this->route . '.update', $data->kategori_potongan_id),
                "data-target"  => "page_kategori_potongan"
            ]);
            if ($data->potongan_type != 1) {
                $button .= Create::action("<i class=\"fas fa-trash\"></i>", [
                    "class"     => "btn btn-danger btn-xs",
                    "onclick"   => "delete_row(this)",
                    "x-token"   => csrf_token(),
                    "data-url"  => route($this->route . ".destroy", $data->kategori_potongan_id),
                ]);
            }
            $button .= Create::action("<i class=\"fas fa-list\"></i>", [
                "class"     => "btn btn-warning btn-xs",
                "onclick"   => "get_list(this,".$data->kategori_potongan_id.")",
            ]);
            return $button;
        })->editColumn('potongan_type',function($data){
            if ($data->potongan_type == 1) {
                $txt = '<label class="badge badge-purple">POTONGAN BY SISTEM</label>';
            }else {
                $txt = '<label class="badge badge-success">POTONGAN TAMBAHAN</label>';
            }
            return $txt;
        })->editColumn('potongan_active',function($data){
            if ($data->potongan_active == 't') {
                $txt = '<label class="badge badge-info">AKTIF</label>';
            }else {
                $txt = '<label class="badge badge-warning">NON AKTIF</label>';
            }
            return $txt;
        })->rawColumns(['action','potongan_type','potongan_active']);
        return $datatables->make(true);
    }

    public function create()
    {
        $kategori_potongan = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('kategori_potongan'));
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
            Kategori_potongan::create($valid['data']);
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

    public function edit(Kategori_potongan $kategori_potongan)
    {
        return view($this->folder . '.form', compact('kategori_potongan'));
    }
    public function update(Request $request, Kategori_potongan $kategori_potongan)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = Kategori_potongan::findOrFail($kategori_potongan->kategori_potongan_id);
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
        $data = Kategori_potongan::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }
}
