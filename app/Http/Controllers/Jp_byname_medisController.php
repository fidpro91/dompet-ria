<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jp_byname_medis;
use App\Models\Komponen_jasa_sistem;
use Illuminate\Support\Facades\Validator;
use DataTables;
use Exception;
use fidpro\builder\Create;
use Illuminate\Support\Facades\DB;

class Jp_byname_medisController extends Controller
{
    public $model   = "Jp_byname_medis";
    public $folder  = "jp_byname_medis";
    public $route   = "jp_byname_medis";

    public $nominalTerima = 0;
    public $param = [
        'skor'              =>  'required',
        'nominal_terima'    =>  '',
        'jaspel_id'         =>  'required',
        'pencairan_id'      =>  '',
        'emp_id'            =>  'required',
        'komponen_id'       =>  'required'
    ];

    public $defaultValue = [
        'jp_medis_id'   =>  '',
        'jaspel_detail_id'   =>  '',
        'kodepegawai'   =>  '',
        'nama_pegawai'   =>  '',
        'skor'   =>  '',
        'nominal_terima'   =>  '',
        'jaspel_id'   =>  '',
        'pencairan_id'   =>  '',
        'emp_id'   =>  '',
        'komponen_id'   =>  ''
    ];
    public function index($jaspel_id)
    {
        $data["komponen"] = Jp_byname_medis::from("jp_byname_medis as jm")
                            ->join("komponen_jasa_sistem as ks","ks.id","=","jm.komponen_id")
                            ->select(["ks.id","ks.nama_komponen"])
                            ->distinct()
                            ->where("jm.jaspel_id",$jaspel_id)
                            ->get();
        return $this->themes($this->folder . '.index', $data, $this);
    }

    public function get_data($id_komponen)
    {
        return view($this->folder.'.data',compact('id_komponen'));
    }

    public function get_dataTable(Request $request)
    {
        $data = Jp_byname_medis::from("jp_byname_medis as jm")
        ->join("employee as e","e.emp_id","=","jm.emp_id")
        ->join("ms_unit as mu","mu.unit_id","=","e.unit_id_kerja")
        ->select(
            [
                'jp_medis_id',
                'e.emp_no',
                'e.emp_name',
                'mu.unit_name',
                'jm.skor',
                'jm.nominal_terima',
                'jaspel_id',
                'pencairan_id',
                'jm.emp_id',
                'jm.komponen_id'
            ]
        );
        $data->where([
            "jaspel_id"     => $request->jaspel_id,
            "komponen_id"   => $request->komponen_id
        ]);

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            /* $button = Create::action("<i class=\"fas fa-edit\"></i>", [
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route . ".edit", $data->jp_medis_id),
                "ajax-url"  => route($this->route . '.update', $data->jp_medis_id),
                "data-target"  => "page_jp_byname_medi"
            ]); */

            $button = Create::action("<i class=\"fas fa-trash\"></i>", [
                "class"     => "btn btn-danger btn-xs",
                "onclick"   => "delete_row(this)",
                "x-token"   => csrf_token(),
                "data-url"  => route($this->route . ".destroy", $data->jp_medis_id),
            ]);
            return $button;
        })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function create()
    {
        $jp_byname_medis = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('jp_byname_medis'));
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
        DB::beginTransaction();
        try {
            //cek apakah pegawai sudah sesuai komponen nya
            $cekByname = Jp_byname_medis::where([
                "jaspel_id"     => $request->jaspel_id,
                "komponen_id"   => $request->komponen_id,
                "emp_id"        => $request->emp_id
            ])->first();
            if ($cekByname) {
                throw new Exception("Jasa pelayanan pegawai sudah dilakukan perhitungan");
            }

            Jp_byname_medis::create($valid['data']);
            $this->sebaran_jaspel($request->jaspel_id,$request->komponen_id);
            DB::commit();
            $resp = [
                'success'   => true,
                'message'   => 'Data Berhasil Disimpan!',
                "redirect"  => $this->get_data($request->komponen_id)->render()
            ];
        } catch (\Exception $e) {
            DB::rollBack();
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

    public function edit(Jp_byname_medis $jp_byname_medis)
    {
        return view($this->folder . '.form', compact('jp_byname_medis'));
    }
    
    public function sebaran_jaspel($jaspel_id,$komponen_id)
    {
        $komponen = Komponen_jasa_sistem::findOrFail($komponen_id);
        //get jaspel detail
        $jaspelDetail = DB::table("jasa_pelayanan_detail")
                        ->where([
                            "jaspel_id"     => $jaspel_id,
                            "komponen_id"   => $komponen->komponen_id_old
                        ])->get()->first();
        if (!$jaspelDetail) {
            $jaspelDetail = Jp_byname_medis::where([
                "jaspel_id"     => $jaspel_id,
                "komponen_id"   => $komponen->id
            ])->selectRaw('SUM(nominal_terima) AS nominal')->first();
            $jaspelDetail->nominal = $jaspelDetail->nominal+$this->nominalTerima;
        }
        //total skor
        $totalSkor = Jp_byname_medis::where([
            "jaspel_id"     => $jaspel_id,
            "komponen_id"   => $komponen->id
        ])->sum("skor");
        
        //sebaran nominal terima
        $byname = Jp_byname_medis::where([
                "jaspel_id"     => $jaspel_id,
                "komponen_id"   => $komponen->id
        ])->get();
        foreach ($byname as $key => $value) {
            $nominalTerima = ($value->skor/$totalSkor)*$jaspelDetail->nominal;
            Jp_byname_medis::find($value->jp_medis_id)->update([
                "nominal_terima"    => $nominalTerima
            ]);
        }
    }

    public function update(Request $request, Jp_byname_medis $jp_byname_medis)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        DB::beginTransaction();
        try {
            $data = Jp_byname_medis::findOrFail($jp_byname_medis->jp_medis_id);
            $data->update($valid['data']);
            $this->sebaran_jaspel($request->jaspel_id,$request->komponen_id);
            DB::commit();
            $resp = [
                'success' => true,
                'message' => 'Data Berhasil Diupdate!'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            $resp = [
                'success' => false,
                'message' => 'Data Gagal Diupdate! <br>' . $e->getMessage()
            ];
        }
        return response()->json($resp);
    }

    public function destroy($id)
    {
        $data = Jp_byname_medis::findOrFail($id);
        DB::beginTransaction();
        try {
            $data->delete();
            $this->nominalTerima = $data->nominal_terima;
            $this->sebaran_jaspel($data->jaspel_id,$data->komponen_id);
            DB::commit();
            $resp = [
                'success' => true,
                'message' => 'Data Berhasil Dihapus!'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            $resp = [
                'success' => false,
                'message' => 'Data Gagal Dihapus! '.$e->getMessage().$e->getLine()
            ];
        }
        return response()->json($resp);
    }
}
