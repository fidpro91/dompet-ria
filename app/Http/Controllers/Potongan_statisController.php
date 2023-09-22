<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Potongan_statis;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;

class Potongan_statisController extends Controller
{
    public $model   = "Potongan_statis";
    public $folder  = "potongan_statis";
    public $route   = "potongan_statis";

    public $param = [
        'pot_stat_code'   =>  '',
        'nama_potongan'   =>  'required',
        'potongan_type'   =>  'required',
        'potongan_nominal'   =>  'required',
        'pot_status'   =>  'required',
        'potongan_note'   =>  '',
        'kategori_potongan'   =>  'required'
    ];
    public $defaultValue = [
        'pot_stat_id'   =>  '',
        'pot_stat_code'   =>  '',
        'nama_potongan'   =>  '',
        'potongan_type'   =>  '',
        'potongan_nominal'   =>  '',
        'pot_status'   =>  't',
        'potongan_note'   =>  '',
        'kategori_potongan'   =>  ''
    ];
    public function index()
    {
        return view($this->folder . '.index');
    }

    public function get_dataTable(Request $request)
    {
        $data = Potongan_statis::where("kategori_potongan",$request->kategori_id)->select([
            'pot_stat_id',
            'pot_stat_code',
            'nama_potongan',
            'potongan_type',
            'potongan_nominal',
            'pot_status',
            'potongan_note',
            'kategori_potongan'
        ])->get();
        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>", [
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route . ".edit", $data->pot_stat_id),
                "ajax-url"  => route($this->route . '.update', $data->pot_stat_id),
                "data-target"  => "page_potongan_stati"
            ]);

            $button .= Create::action("<i class=\"fas fa-trash\"></i>", [
                "class"     => "btn btn-danger btn-xs",
                "onclick"   => "delete_row(this)",
                "x-token"   => csrf_token(),
                "data-url"  => route($this->route . ".destroy", $data->pot_stat_id),
            ]);
            return $button;
        })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function create()
    {
        $potongan_statis = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('potongan_statis'));
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
            Potongan_statis::create($valid['data']);
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

    public function edit(Potongan_statis $potongan_statis)
    {
        return view($this->folder . '.form', compact('potongan_statis'));
    }
    public function update(Request $request, Potongan_statis $potongan_statis)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = Potongan_statis::findOrFail($potongan_statis->pot_stat_id);
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
        $data = Potongan_statis::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }
}
