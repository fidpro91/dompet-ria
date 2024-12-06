<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Detail_skor_pegawai;
use App\Models\Skor_pegawai;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;

class Detail_skor_pegawaiController extends Controller
{
    public $model   = "Detail_skor_pegawai";
    public $folder  = "detail_skor_pegawai";
    public $route   = "detail_skor_pegawai";

    public $param = [
        'skor_id'   =>  'required',
        'emp_id'   =>  'required',
        'kode_skor'   =>  'required',
        'detail_skor'   =>  '',
        'skor'   =>  ''
    ];

    public $defaultValue = [
        'det_skor_id'   =>  '',
        'skor_id'   =>  '',
        'emp_id'   =>  '',
        'kode_skor'   =>  '',
        'detail_skor'   =>  '',
        'skor'   =>  ''
    ];
    public function index()
    {
        return $this->themes($this->folder . '.index', null, $this);
    }

    public function get_dataTable(Request $request)
    {
        $data = Detail_skor_pegawai::select(
            [
                'det_skor_id',
                'skor_id',
                'emp_id',
                'kode_skor',
                'detail_skor',
                'skor'
            ]
        );

        $data->where([
            "skor_id"   => $request->id_skor,
            "kode_skor" => $request->kode_skor
        ]);

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>", [
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route . ".edit", $data->det_skor_id),
                "ajax-url"  => route($this->route . '.update', $data->det_skor_id),
                "data-target"  => "page_detail_skor_pegawai"
            ]);

            $button .= Create::action("<i class=\"fas fa-trash\"></i>", [
                "class"     => "btn btn-danger btn-xs",
                "onclick"   => "delete_row(this)",
                "x-token"   => csrf_token(),
                "data-url"  => route($this->route . ".destroy", $data->det_skor_id),
            ]);
            return $button;
        })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function create()
    {
        $detail_skor_pegawai = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('detail_skor_pegawai'));
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
            Detail_skor_pegawai::create($valid['data']);
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

    public function data($kodeskor)
    {
        return view($this->folder . '.index', compact('kodeskor'));
    }

    public function edit(Detail_skor_pegawai $detail_skor_pegawai)
    {
        return view($this->folder . '.form', compact('detail_skor_pegawai'));
    }

    public function update(Request $request, Detail_skor_pegawai $detail_skor_pegawai)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = Detail_skor_pegawai::findOrFail($detail_skor_pegawai->det_skor_id);
            $data->update($valid['data']);
            $sumSkor = Detail_skor_pegawai::where("skor_id",$request->skor_id)->sum("skor");
            Skor_pegawai::find($request->skor_id)->update([
                "total_skor"    => $sumSkor,
                "skor_type"     => 2
            ]);

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
        $data = Detail_skor_pegawai::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }
}
