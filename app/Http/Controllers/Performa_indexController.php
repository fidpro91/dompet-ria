<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use Illuminate\Http\Request;
use App\Models\Performa_index;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;

class Performa_indexController extends Controller
{
    public $model   = "Performa_index";
    public $folder  = "performa_index";
    public $route   = "performa_index";

    public $param = [
        'tanggal_perform'   =>  'required',
        'emp_id'   =>  'required',
        'perform_id'   =>  'required',
        'perform_skor'   =>  'required',
        'perform_deskripsi'   =>  '',
        'expired_date'   =>  'required'
    ];
    public $defaultValue = [
        'id'   =>  '',
        'tanggal_perform'   =>  '',
        'emp_id'   =>  '',
        'perform_id'   =>  '',
        'perform_skor'   =>  '',
        'perform_deskripsi'   =>  '',
        'expired_date'  => ''
    ];
    public function index()
    {
        return $this->themes($this->folder . '.group_performa', null, $this);
    }

    public function data($performa_id)
    {
        return view($this->folder . '.data', compact('performa_id'));
    }

    public function get_dataTable(Request $request)
    {
        $data = Performa_index::from("performa_index as pi")
            ->where("pi.perform_id",$request->performa_id)
            ->when($request->bulan_update, function ($query) use ($request) {
                return $query->whereMonth('pi.expired_date', $request->bulan_update);
            })
            ->join("employee as e","e.emp_id","=","pi.emp_id")
            ->join("detail_indikator as di","di.detail_id","=","pi.perform_skor")
            ->select([
                'id',
                'tanggal_perform',
                'emp_no',
                'emp_name',
                'perform_deskripsi',
                'di.detail_name',
                'di.skor',
            ]);
        
        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            /* $button = Create::action("<i class=\"fas fa-edit\"></i>", [
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route . ".edit", $data->id),
                "ajax-url"  => route($this->route . '.update', $data->id),
                "data-target"  => "page_performa_index"
            ]); */

            $button = Create::action("<i class=\"fas fa-trash\"></i>", [
                "class"     => "btn btn-danger btn-xs",
                "onclick"   => "delete_row(this)",
                "x-token"   => csrf_token(),
                "data-url"  => route($this->route . ".destroy", $data->id),
            ]);
            return $button;
        })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function create($id)
    {
        $this->defaultValue['perform_id'] = $id;
        $indikator = Indikator::where("group_index",$id)->first();
        $this->defaultValue['indikator_id'] = $indikator->id;
        $performa_index = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('performa_index'));
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
            $valid['data']['tanggal_perform'] = date('Y-m-d',strtotime($request->tanggal_perform));
            $valid['data']['expired_date'] = date_db($request->expired_date);
            Performa_index::create($valid['data']);
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

    public function edit(Performa_index $performa_index)
    {
        return view($this->folder . '.form', compact('performa_index'));
    }
    public function update(Request $request, Performa_index $performa_index)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = Performa_index::findOrFail($performa_index->id);
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
        $data = Performa_index::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }
}
