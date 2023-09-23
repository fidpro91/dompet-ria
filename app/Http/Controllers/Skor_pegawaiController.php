<?php

namespace App\Http\Controllers;

use App\Models\Detail_indikator;
use App\Models\Diklat;
use App\Models\Employee;
use App\Models\Employee_off;
use App\Models\Ms_unit;
use App\Models\Performa_index;
use Illuminate\Http\Request;
use App\Models\Skor_pegawai;
use App\Models\Tugas_tambahan;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Skor_pegawaiController extends Controller
{
    public $model   = "Skor_pegawai";
    public $folder  = "skor_pegawai";
    public $route   = "skor_pegawai";

    public $param = [
        'basic_index'   =>  'required',
        'capacity_index'   =>  'required',
        'emergency_index'   =>  'required',
        'unit_risk_index'   =>  'required',
        'position_index'   =>  'required',
        'competency_index'   =>  'required',
        'total_skor'   =>  'required',
        'bulan_update'   =>  'required',
        'emp_id'   =>  'required',
        'skor_type' =>  'required'
        /* 'admin_risk_index'   =>  '',
        'is_confirm'   =>  '',
        'confirm_by'   =>  '' */
    ];
    
    public $defaultValue = [
        'id'   =>  '',
        'basic_index'   =>  '',
        'capacity_index'   =>  '',
        'emergency_index'   =>  '',
        'unit_risk_index'   =>  '',
        'position_index'   =>  '',
        'competency_index'   =>  '',
        'total_skor'   =>  '',
        'bulan_update'   =>  '',
        'emp_id'   =>  '',
        'admin_risk_index'   =>  '',
        'skor_type' =>  ''
    ];

    public $session;

    public function __construct()
    {
        $this->session = Session::get('sesLogin');
    }

    public function index()
    {
        return $this->themes($this->folder . '.index', null, $this);
    }

    public function get_data()
    {
        return view($this->folder . '.data');
    }

    public function hasil_skor()
    {
        return $this->themes($this->folder . '.hasil_skor', null, 'Hasil Perhitungan Skor Individu');
    }

    public function save_skor()
    {
        $skor = Cache::get('skorPegawai');
        DB::beginTransaction();
        try {
            
            foreach ($skor as $key => $value) {
                $skoring = [
                    'basic_index'   =>  $value['dataSkor']['basic']['skor'],
                    'capacity_index'   =>  $value['dataSkor']['capacity']['skor'],
                    'emergency_index'   =>  $value['dataSkor']['emergency']['skor'],
                    'unit_risk_index'   =>  $value['dataSkor']['risk']['skor'],
                    'position_index'   =>  ($value['dataSkor']['position']['skor']+$value['dataSkor']['tugas']['skor']),
                    'competency_index'   =>  $value['dataSkor']['performa']['skor'],
                    'total_skor'        =>  $value['totalSkor'],
                    'skor_note'         =>  $value['skor_note'],
                    'bulan_update'   =>  $value['bulan_update'],
                    'emp_id'   =>  $value['id'],
                    'created_by'  => Auth::user()->id,
                    'skor_type'   => 1
                ];

                //delete duplikat skor
                Skor_pegawai::where([
                    "emp_id"        => $skoring["emp_id"],
                    "bulan_update"  => $skoring["bulan_update"]
                ])->delete();

                $save=Skor_pegawai::create($skoring);
                $lastId = $save->id;
                foreach ($value['dataSkor'] as $key => $rs) {
                    $detailSkor = [
                        "skor_id"       => $lastId,
                        "emp_id"        => $value['id'],
                        "kode_skor"     => $key,
                        "detail_skor"   => $rs['keterangan'],
                        "skor"          => $rs['skor']
                    ];
                    DB::table('detail_skor_pegawai')->insert($detailSkor);
                }
            }
            $resp = [
                'success' => true,
                'message' => 'Data Berhasil Disimpan!'
            ];
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            $resp = [
                'success' => false,
                'message' => 'Data gagal disimpan! <br>'.$e->getMessage()
            ];
        }
        return response()->json($resp);
    }

    public function get_dataTable(Request $request)
    {
        $data = Skor_pegawai::from("skor_pegawai as sp")
                ->join("employee as e","e.emp_id","=","sp.emp_id")
                ->join("ms_unit as mu","mu.unit_id","=","e.unit_id_Kerja")
                ->select([
                    'id',
                    'e.emp_no',
                    'e.emp_name',
                    'mu.unit_name',
                    'basic_index',
                    'capacity_index',
                    'emergency_index',
                    'unit_risk_index',
                    'position_index',
                    'competency_index',
                    'total_skor',
                    'skor_koreksi',
                    'bulan_update',
                    'sp.emp_id',
                    'admin_risk_index',
                    'is_confirm',
                    'confirm_by'
                ]);
        if ($request->prepare_remun) {
            $data->where("prepare_remun",$request->prepare_remun);
        }
        if ($request->bulan_update) {
            $data->where("bulan_update",$request->bulan_update);
        }

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>", [
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route . ".edit", $data->id),
                "ajax-url"  => route($this->route . '.update', $data->id),
                "data-target"  => "page_skor_pegawai"
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
        $skor_pegawai = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('skor_pegawai'));
    }

    public function store(Request $request)
    {
        $request['skor_type'] = 2;
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $valid['message']
            ]);
        }
        try {
            Skor_pegawai::create($valid['data']);
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

    public function generate_skor(Request $request)
    {
        $data=[];
        Cache::forget('skorPegawai');
        Cache::forget('skorError');
        try {
            if ($request->emp_id_gen) {
                $pegawai =  Employee::from("employee as e")
                            ->where("e.emp_id",$request->emp_id_gen)
                            ->join("ms_unit as mu","mu.unit_id","=","e.unit_id_kerja")
                            ->join("detail_indikator as di","di.detail_id","=","e.pendidikan")
                            ->join("indikator as i","i.id","=","di.indikator_id")
                            ->select(["e.*","di.detail_name as pendidikan","di.skor as skor_pendidikan","di.indikator_id","i.bobot","mu.unit_name"])
                            ->get();
            }elseif ($request->unit_id) {
                $pegawai =  Employee::from("employee as e")
                            ->where("e.unit_id_kerja",$request->unit_id)
                            ->join("ms_unit as mu","mu.unit_id","=","e.unit_id_kerja")
                            ->join("detail_indikator as di","di.detail_id","=","e.pendidikan")
                            ->join("indikator as i","i.id","=","di.indikator_id")
                            ->select(["e.*","di.detail_name as pendidikan","di.skor as skor_pendidikan","di.indikator_id","i.bobot","mu.unit_name"])
                            ->get();
            }else{
                $pegawai =  Employee::from("employee as e")
                            ->where("e.emp_active","t")
                            ->join("ms_unit as mu","mu.unit_id","=","e.unit_id_kerja")
                            ->join("detail_indikator as di","di.detail_id","=","e.pendidikan")
                            ->join("indikator as i","i.id","=","di.indikator_id")
                            ->select(["e.*","di.detail_name as pendidikan","di.skor as skor_pendidikan","di.indikator_id","i.bobot","mu.unit_name"])
                            ->get();
            }
            foreach ($pegawai as $key => $pgw) {
                $data[$key] = [
                    "nip"   => $pgw->emp_no,
                    "id"    => $pgw->emp_id,
                    "bulan_update"    => $request->bulan_skor,
                    "nama"  => $pgw->emp_name
                ];

                $skor=($pgw->gaji_pokok/1000000*1);
                $data[$key]['dataSkor']['basic'] = [
                    "skor"          => $skor,
                    "keterangan"    => "BASIC INDEX BERDASARKAN GAJI POKOK"
                ];
                $data[$key]['totalSkor'] = $skor;
                $skor=($pgw->skor_pendidikan*$pgw->bobot);
                $data[$key]['totalSkor'] = $data[$key]['totalSkor'] +$skor;
                $data[$key]['dataSkor']['capacity'] = [
                    "skor"          => $skor,
                    "keterangan"    => "BASIC INDEX PENDIDIKAN - ".$pgw->pendidikan
                ];

                $data[$key]['totalSkor'] = $data[$key]['totalSkor'] + $pgw->unit_risk_index;
                $data[$key]['dataSkor']['risk'] = [
                    "skor"          => $pgw->unit_risk_index,
                    "keterangan"    => "RISK INDEX (INFEKSIUS+ADMINISTRASI) - ".$pgw->unit_name
                ];

                $data[$key]['totalSkor'] = $data[$key]['totalSkor'] + $pgw->unit_emergency_index;
                $data[$key]['dataSkor']['emergency'] = [
                    "skor"          => $pgw->unit_emergency_index,
                    "keterangan"    => "RISK INDEX (INFEKSIUS+ADMINISTRASI) - ".$pgw->unit_name
                ];

                //get jabatan skor
                if ($pgw->jabatan_struktural) {
                    $posisi = Detail_indikator::from("detail_indikator as di")
                              ->where("detail_id",$pgw->jabatan_struktural)
                              ->join("indikator as i","i.id","=","di.indikator_id")->first();
                }elseif ($pgw->jabatan_fungsional) {
                    $posisi = Detail_indikator::from("detail_indikator as di")
                              ->where("detail_id",$pgw->jabatan_fungsional)
                              ->join("indikator as i","i.id","=","di.indikator_id")->first();
                }
                $skor = ($posisi->skor*$posisi->bobot);
                $data[$key]['totalSkor'] = $data[$key]['totalSkor'] +$skor;
                $data[$key]['dataSkor']['position'] = [
                    "skor"          => $skor,
                    "keterangan"    => "POSITION INDEX - ".$posisi->detail_name
                ];
                /* $sertifikatNote = implode(',',$sertifikatNote);
                $data[$key]['dataSkor']['certifycate'] = [
                    "skor"          => ($sertifikatSkor),
                    "keterangan"    => ($sertifikatSkor>0)?"DIKLAT & SERTIFIKAT INDEX - ($sertifikatNote)":NULL
                ];
                $data[$key]['totalSkor'] = $data[$key]['totalSkor'] +$sertifikatSkor; */
                
            }
            Cache::add('skorPegawai',array_values($data),3000);
            Cache::remember('skorError', 3000, function () use($data){
                $emp_id = implode(',',array_column($data, 'id'));
                $dataError = DB::select("
                            select e.emp_no,e.emp_name,mu.unit_name from employee e 
                            join ms_unit mu on mu.unit_id = e.unit_id_kerja
                            where e.emp_active = 't' and e.emp_id not in (".$emp_id.")
                        ");
                return $dataError;
            });
            $resp = [
                'success' => false,
                'message' => 'Skor Berhasil Digenerate!'
            ];
        } catch (\Exception $e) {
            $resp = [
                'success' => false,
                'message' => 'Data Gagal Digenerate! <br>' . $e->getMessage(). " Line ",$e->getLine()
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
                "message"   => implode('<br>',$validator->errors()->all())
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

    public function edit(Skor_pegawai $skor_pegawai)
    {
        return view($this->folder . '.form', compact('skor_pegawai'));
    }
    public function update(Request $request, Skor_pegawai $skor_pegawai)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = Skor_pegawai::findOrFail($skor_pegawai->id);
            $valid['data']['skor_type'] = 2;
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
        $data = Skor_pegawai::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }
    
    public function clear_all_data(Request $request)
    {
        $data = Skor_pegawai::where("bulan_update",$request->bulan_skor);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }

    public function error_skor()
    {
        $data = Cache::get('skorError');
        $table = \fidpro\builder\Bootstrap::tableData($data,["class"=>"table table-bordered table-error"]);
        $table .= "
        <script>
            $('.table-error').DataTable();
        </script>";
        return $table;
    }
}
