<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Komplain_skor;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;

class Komplain_skorController extends Controller
{
    public $model   = "Komplain_skor";
    public $folder  = "komplain_skor";
    public $route   = "komplain_skor";

    public $param = [
        'tanggal'   =>  'required',
        'id_skor'   =>  'required',
        'employee_id'   =>  'required',
        'isi_komplain'   =>  '',
        'tanggapan_komplain'   =>  '',
        'status_komplain'   =>  '',
        'user_komplain'   =>  '',
        'user_approve'   =>  ''
    ];
    public $defaultValue = [
        'id_komplain'   =>  '',
        'tanggal'   =>  'CURRENT_TIMESTAMP',
        'id_skor'   =>  '',
        'employee_id'   =>  '',
        'isi_komplain'   =>  '',
        'tanggapan_komplain'   =>  '',
        'status_komplain'   =>  '',
        'user_komplain'   =>  '',
        'user_approve'   =>  ''
    ];
    public function index()
    {
        return $this->themes($this->folder . '.index', null, $this);
    }

    public function get_dataTable(Request $request)
    {
        $data = Komplain_skor::from("komplain_skor as ks")
                ->join("skor_pegawai as sp","sp.id","=","ks.id_skor")
                ->join("employee as e","e.emp_id","=","sp.emp_id")
                ->select(
                    [
                        'ks.*',
                        'e.emp_no',
                        'e.emp_name',
                        'sp.total_skor'
                    ]
                );

        $data->where("sp.bulan_update",$request->bulan_skor);
        if ($request->unit_kerja) {
            $data->where("e.unit_id_kerja",$request->unit_kerja);
        }
        if ($request->status_komplain) {
            $data->where("ks.status_komplain",$request->status_komplain);
        }
        
        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>", [
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route . ".edit", $data->id_komplain),
                "ajax-url"  => route($this->route . '.update', $data->id_komplain),
                "data-target"  => "page_komplain_skor"
            ]);

            $button .= Create::action("<i class=\"fas fa-trash\"></i>", [
                "class"     => "btn btn-danger btn-xs",
                "onclick"   => "delete_row(this)",
                "x-token"   => csrf_token(),
                "data-url"  => route($this->route . ".destroy", $data->id_komplain),
            ]);
            return $button;
        })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function create()
    {
        $komplain_skor = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('komplain_skor'));
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
            Komplain_skor::create($valid['data']);
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

    public function edit(Komplain_skor $komplain_skor)
    {
        return view($this->folder . '.form', compact('komplain_skor'));
    }
    public function update(Request $request, Komplain_skor $komplain_skor)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = Komplain_skor::findOrFail($komplain_skor->id_komplain);
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
        $data = Komplain_skor::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }
}
