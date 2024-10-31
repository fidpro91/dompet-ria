<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table_rekap_absen;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;

class Table_rekap_absenController extends Controller
{
    public $model   = "Table_rekap_absen";
    public $folder  = "table_rekap_absen";
    public $route   = "table_rekap_absen";

    public $param = [
        'nip'   =>  '',
        'bulan_update'   =>  '',
        'tahun_update'   =>  '',
        'persentase_kehadiran'   =>  '',
        'keterangan'   =>  '',
        'created_at'   =>  '',
        'updated_at'   =>  ''
    ];
    public $defaultValue = [
        'id'   =>  '',
        'nip'   =>  '',
        'bulan_update'   =>  '',
        'tahun_update'   =>  '',
        'persentase_kehadiran'   =>  '',
        'keterangan'   =>  '',
        'created_at'   =>  '',
        'updated_at'   =>  ''
    ];
    public function index()
    {
        return $this->themes($this->folder . '.index', null, $this);
    }

    public function get_dataTable(Request $request)
    {
        $data = Table_rekap_absen::select(
            [
                'id',
                'nip',
                'bulan_update',
                'tahun_update',
                'persentase_kehadiran',
                'keterangan',
                'created_at',
                'updated_at'
            ]
        );

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>", [
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route . ".edit", $data->id),
                "ajax-url"  => route($this->route . '.update', $data->id),
                "data-target"  => "page_table_rekap_absen"
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
        $table_rekap_absen = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('table_rekap_absen'));
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
            Table_rekap_absen::create($valid['data']);
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

    public function edit(Table_rekap_absen $table_rekap_absen)
    {
        return view($this->folder . '.form', compact('table_rekap_absen'));
    }
    public function update(Request $request, Table_rekap_absen $table_rekap_absen)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = Table_rekap_absen::findOrFail($table_rekap_absen->id);
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
        $data = Table_rekap_absen::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }
}
