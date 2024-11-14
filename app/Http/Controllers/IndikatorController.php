<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateSkor;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\Indikator;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;
use Illuminate\Support\Facades\DB;

class IndikatorController extends Controller
{
    public $model   = "Indikator";
    public $folder  = "indikator";
    public $route   = "indikator";

    public $param = [
        'kode_indikator'   =>  'required',
        'indikator'   =>  'required',
        'bobot'   =>  'required',
        'group_index'   =>  '',
        'status'   =>  'required'
    ];
    public $defaultValue = [
        'id'   =>  '',
        'kode_indikator'   =>  '',
        'indikator'   =>  '',
        'bobot'   =>  '',
        'deskripsi'   =>  '',
        'group_index'   =>  '',
        'status'   =>  't'
    ];
    public function index()
    {
        return $this->themes($this->folder . '.index', null, $this);
    }

    public function get_dataTable(Request $request)
    {
        $data = Indikator::select([
            'id',
            'kode_indikator',
            'indikator',
            'deskripsi',
            'bobot',
            'status'
        ]);

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>", [
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route . ".edit", $data->id),
                "ajax-url"  => route($this->route . '.update', $data->id),
                "data-target"  => "page_indikator"
            ]);
            $button .= Create::action("<i class=\"fas fa-trash\"></i>", [
                "class"     => "btn btn-danger btn-xs",
                "onclick"   => "delete_row(this)",
                "x-token"   => csrf_token(),
                "data-url"  => route($this->route . ".destroy", $data->id),
            ]);
            $button .= Create::action("<i class=\"fas fa-list\"></i>", [
                "class"     => "btn btn-info btn-xs",
                "onclick"   => "get_list(this,".$data->id.")",
            ]);
            return $button;
        })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function create()
    {
        $indikator = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('indikator'));
    }

    public function store(Request $request)
    {
        $request['status'] = $request->status_indikator;
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            Indikator::create($valid['data']);
            $resp = [
                'success' => true,
                'message' => 'Data Berhasil Disimpan!'
            ];
        }catch(\Exception $e){
            $resp = [
                'success' => false,
                'message' => 'Data Gagal Disimpan! <br>'.$e->getMessage()
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

    public function edit(Indikator $indikator)
    {
        return view($this->folder . '.form', compact('indikator'));
    }
    
    public function update(Request $request, Indikator $indikator)
    {
        $request['status'] = $request->status_indikator;
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        DB::beginTransaction();
        try {
            $data = Indikator::findOrFail($indikator->id);
            $data->update($valid['data']);
            //update pegawai
            UpdateSkor::dispatch();
            
            DB::commit();
            $resp = [
                'success' => true,
                'message' => 'Data Berhasil Diupdate!'
            ];
        }catch(\Exception $e){
            DB::rollBack();
            $resp = [
                'success' => false,
                'message' => 'Data Gagal Diupdate! <br>'.$e->getMessage()
            ];
        }
        return response()->json($resp);
    }

    public function destroy($id)
    {
        $data = Indikator::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diudapte!'
        ]);
    }
}
