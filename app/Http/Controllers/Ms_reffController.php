<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ms_reff;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;

class Ms_reffController extends Controller
{
    public $model   = "Ms_reff";
    public $folder  = "ms_reff";
    public $route   = "ms_reff";

    public $param = [
        'reff_code'   =>  '',
        'reff_name'   =>  'required',
        'reff_active'   =>  'required',
        'reffcat_id'   =>  'required'
    ];
    public $defaultValue = [
        'reff_id'   =>  '',
        'reff_code'   =>  '',
        'reff_name'   =>  '',
        'reff_active'   =>  '',
        'reffcat_id'   =>  ''
    ];
    public function index()
    {
        return $this->themes($this->folder . '.index', null, $this);
    }

    public function data($id)
    {
        return view($this->folder . '.index', compact('id'));
    }

    public function get_dataTable(Request $request)
    {
        $data = Ms_reff::where("reffcat_id",$request->reffcat_id)->select([
            'reff_id',
            'reff_code',
            'reff_name',
            'reff_active',
            'reffcat_id'
        ])->get();

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>", [
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route . ".edit", $data->reff_id),
                "ajax-url"  => route($this->route . '.update', $data->reff_id),
                "data-target"  => "page_ms_reff"
            ]);

            $button .= Create::action("<i class=\"fas fa-trash\"></i>", [
                "class"     => "btn btn-danger btn-xs",
                "onclick"   => "delete_row(this)",
                "x-token"   => csrf_token(),
                "data-url"  => route($this->route . ".destroy", $data->reff_id),
            ]);
            return $button;
        })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function create()
    {
        $ms_reff = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('ms_reff'));
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
            Ms_reff::create($valid['data']);
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

    public function edit(Ms_reff $ms_reff)
    {
        return view($this->folder . '.form', compact('ms_reff'));
    }
    public function update(Request $request, Ms_reff $ms_reff)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = Ms_reff::findOrFail($ms_reff->reff_id);
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
        $data = Ms_reff::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }
}
