<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Komplain_skor;
use App\Models\Ms_reff;
use App\Models\Skor_pegawai;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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
        
        $datatables = DataTables::of($data)->addIndexColumn()
        ->editColumn('isi_komplain', function ($data) {
            $template = Cache::rememberForever('template_chat', function () {
                return Ms_reff::where('reffcat_id', 10)->get();
            });
            $html = view("komplain_skor.v_response_komplain",compact('data','template'));
            return $html;
        })->editColumn('emp_name', function ($data) {
            $html = $data->emp_no."<br>".$data->emp_name."<br><b>".$data->employee->unit->unit_name."<b>";
            return $html;
        })->editColumn('total_skor', function ($data) {
            $html = Create::action("<i class=\"fas fa-eye\"></i> $data->total_skor", [
                "class"     => "btn btn-danger",
                "onclick"   => "get_info(this,$data->id_komplain,$data->id_skor)",
            ]);
            return $html;
        })->rawColumns(['action','isi_komplain','emp_name','total_skor']);
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
    
    public function get_data_skor($id)
    {
        $data = Skor_pegawai::from("skor_pegawai as sp")
                ->join("employee as e","e.emp_id","=","sp.emp_id")
                ->join("ms_unit as mu","mu.unit_id","=","e.unit_id_kerja")
                ->where("sp.id",$id)
                ->select([
                    "e.emp_no",
                    "e.emp_name",
                    "mu.unit_name",
                    'sp.basic_index',
                    'sp.capacity_index',
                    'sp.emergency_index',
                    'sp.unit_risk_index',
                    'sp.admin_risk_index',
                    'sp.position_index',
                    'sp.competency_index',
                    'sp.total_skor',
                ])->first()->toArray();
        return view($this->folder . '.data_skor',compact('data'));
    }

    public function update(Request $request)
    {
        try {
            $data = Komplain_skor::findOrFail($request->id_komplain);
            $data->update([
                "user_approve"          => Auth::id(),
                "status_komplain"       => 2,
                "tanggapan_komplain"    => $request->tanggapan_komplain
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
        $data = Komplain_skor::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }
}
