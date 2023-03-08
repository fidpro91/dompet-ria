<?php

namespace App\Http\Controllers;

use App\Models\Ms_reff;
use Illuminate\Http\Request;
use App\Models\Repository_download;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;

class Repository_downloadController extends Controller
{
    public $model   = "Repository_download";
    public $folder  = "repository_download";
    public $route   = "repository_download";

    public $param = [
        'download_date'   =>  'required',
        'bulan_jasa'   =>  'required',
        'bulan_pelayanan'   =>  'required',
        'periode_awal'   =>  'required',
        'periode_akhir'   =>  'required',
        'group_penjamin'   =>  '',
        'jenis_pembayaran'   =>  'required',
        'download_by'   =>  '',
        'download_no'   => 'required',
    ];
    public $defaultValue = [
        'id'   =>  '',
        'download_date'   =>  '',
        'bulan_jasa'   =>  '',
        'bulan_pelayanan'   =>  '',
        'periode_awal'   =>  '',
        'periode_akhir'   =>  '',
        'group_penjamin'   =>  '',
        'jenis_pembayaran'   =>  '',
        'download_by'   =>  '',
    ];
    public function index()
    {
        return $this->themes($this->folder . '.index', null, $this);
    }

    public function get_dataTable(Request $request)
    {
        $data = Repository_download::select([
            'id',
            'download_date',
            'download_no',
            'bulan_jasa',
            'bulan_pelayanan',
            'periode_awal',
            'periode_akhir',
            'group_penjamin',
            'jenis_pembayaran',
            'download_by'
        ]);
        if ($request->is_used) {
            $data->where("is_used",$request->is_used);
        }

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-trash\"></i>", [
                "class"     => "btn btn-danger btn-xs",
                "onclick"   => "delete_row(this)",
                "x-token"   => csrf_token(),
                "data-url"  => route($this->route . ".destroy", $data->id),
            ]);
            return $button;
        })->editColumn('group_penjamin',function($data){
            $penjamin = "ALL";
            if (!empty($data->group_penjamin)) {
                $penjamin = json_decode($data->group_penjamin,true);
                $penjamin = Ms_reff::whereIn("reff_code",$penjamin)->where('reffcat_id',5)->pluck("reff_name")->toArray();
                $penjamin = implode(",",$penjamin);
            }
            return $penjamin;            
        })->editColumn('jenis_pembayaran',function($data){
            if ($data->jenis_pembayaran == 1) {
                return "Tunai";
            }else{
                return "Piutang";
            }
        })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function create()
    {
        $repository_download = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('repository_download'));
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
            Repository_download::create($valid['data']);
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

    public function edit(Repository_download $repository_download)
    {
        return view($this->folder . '.form', compact('repository_download'));
    }
    public function update(Request $request, Repository_download $repository_download)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = Repository_download::findOrFail($repository_download->id);
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
        $data = Repository_download::findOrFail($id);
        if ($data->is_used == 't') {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak dapat dihapus karena sudah digunakan!'
            ]);
        }
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }
}
