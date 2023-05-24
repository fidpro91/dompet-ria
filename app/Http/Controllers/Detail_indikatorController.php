<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Detail_indikator;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;

class Detail_indikatorController extends Controller
{
    public $model   = "Detail_indikator";
    public $folder  = "detail_indikator";
    public $route   = "detail_indikator";

    public $param = [
        'indikator_id'   =>  'required',
        'detail_name'   =>  'required',
        'skor'   =>  'required',
        'detail_status'   =>  '',
        'detail_deskripsi'   =>  ''
    ];
    public $defaultValue = [
        'detail_id'   =>  '',
        'indikator_id'   =>  '',
        'detail_name'   =>  '',
        'skor'   =>  '',
        'detail_status'   =>  '',
        'detail_deskripsi'   =>  ''
    ];
    public function index()
    {
        return $this->themes($this->folder . '.index', null, $this);
    }

    public function get_dataTable(Request $request)
    {
        $data = Detail_indikator::where([
                    "indikator_id"  => $request->indikator_id
                ])->select([
                    'detail_id',
                    'detail_name',
                    'detail_deskripsi',
                    'skor',
                    'detail_status'
                ])->get();

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>", [
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route . ".edit", $data->detail_id),
                "ajax-url"  => route($this->route . '.update', $data->detail_id),
                "data-target"  => "page_detail_indikator"
            ]);

            $button .= Create::action("<i class=\"fas fa-trash\"></i>", [
                "class"     => "btn btn-danger btn-xs",
                "onclick"   => "delete_row(this)",
                "x-token"   => csrf_token(),
                "data-url"  => route($this->route . ".destroy", $data->detail_id),
            ]);
            return $button;
        })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function create()
    {
        $detail_indikator = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('detail_indikator'));
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
            Detail_indikator::create($valid['data']);
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

    public function edit(Detail_indikator $detail_indikator)
    {
        return view($this->folder . '.form', compact('detail_indikator'));
    }
    public function update(Request $request, Detail_indikator $detail_indikator)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = Detail_indikator::findOrFail($detail_indikator->detail_id);
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
        $data = Detail_indikator::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diudapte!'
        ]);
    }
}
