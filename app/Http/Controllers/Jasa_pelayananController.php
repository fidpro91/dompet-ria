<?php

namespace App\Http\Controllers;

use App\Exports\JaspelExport;
use App\Libraries\Servant;
use App\Models\Detail_tindakan_medis;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\Jasa_pelayanan;
use App\Models\Komponen_jasa;
use App\Models\Komponen_jasa_sistem;
use App\Models\Proporsi_jasa_individu;
use App\Models\Repository_download;
use App\Models\Skor_pegawai;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class Jasa_pelayananController extends Controller
{
    public $model   = "Jasa_pelayanan";
    public $folder  = "jasa_pelayanan";
    public $route   = "jasa_pelayanan";

    public $param = [
        'tanggal_jaspel'   =>  'required',
        'periode_jaspel'   =>  '',
        'jaspel_bulan'   =>  'required',
        'jaspel_tahun'   =>  'required',
        'kodejaminan'   =>  '',
        'namajaminan'   =>  '',
        'nominal_pendapatan'   =>  'required',
        'percentase_jaspel'   =>  'required',
        'nominal_jaspel'   =>  'required',
        'created_by'   =>  '',
        'created_at'   =>  '',
        'status'   =>  '',
        'keterangan'   =>  '',
        'id_cair'   =>  '',
        'repo_id'   =>  '',
        'no_jasa'   =>  ''
    ];

    public $defaultValue = [
        'jaspel_id'   =>  '',
        'tanggal_jaspel'   =>  '',
        'periode_jaspel'   =>  '',
        'jaspel_bulan'   =>  '',
        'jaspel_tahun'   =>  '',
        'kodejaminan'   =>  '',
        'namajaminan'   =>  '',
        'nominal_pendapatan'   =>  '',
        'percentase_jaspel'   =>  '',
        'nominal_jaspel'   =>  '',
        'created_by'   =>  '',
        'created_at'   =>  '',
        'status'   =>  '',
        'keterangan'   =>  '',
        'id_cair'   =>  '',
        'no_jasa'   =>  ''
    ];
    public function index()
    {
        return $this->themes($this->folder . '.index3', null, $this);
    }

    public function list()
    {
        return $this->themes($this->folder . '.index', null, "List Riwayat Perhitungan Jasa");
    }

    public function get_dataTable(Request $request)
    {
        $data = Jasa_pelayanan::select([
            'jaspel_id',
            'tanggal_jaspel',
            'periode_jaspel',
            'jaspel_bulan',
            'jaspel_tahun',
            'kodejaminan',
            'namajaminan',
            'nominal_pendapatan',
            'percentase_jaspel',
            'nominal_jaspel',
            'created_by',
            'created_at',
            'status',
            'keterangan',
            'id_cair',
            'no_jasa',
            'repo_id'
        ])->orderBy("jaspel_id","desc");

        if ($request->status) {
            $data->where("status",$request->status);
        }
        if ($request->id_cair) {
            $data->where("id_cair",$request->id_cair);
        }

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            /* $button = Create::link("<i class=\"far fa-file-pdf\"></i>", [
                "class"     => "btn btn-info btn-xs",
                "href"      => url("jasa_pelayanan/print/$data->jaspel_id"),
                "target"    => "_blank"
            ]); */
            $button = Create::link("<i class=\"far fa-file-excel\"></i>", [
                "class"     => "btn btn-success btn-xs",
                "href"      => url("jasa_pelayanan/excel/$data->jaspel_id"),
            ]);
            $button .= Create::action("<i class=\"fas fa-print\"></i>", [
                "class"     => "btn btn-secondary btn-xs",
                "onclick"   => "open_print(".$data->jaspel_id.")"
            ]);
            if (!$data->id_cair) {
                $button .= Create::action("<i class=\"fas fa-edit\"></i>", [
                    "class"     => "btn btn-primary btn-xs",
                    "onclick"   => "set_editable($data->jaspel_id)",
                ]);
                
                $button .= Create::action("<i class=\"fas fa-trash\"></i>", [
                    "class"     => "btn btn-danger btn-xs",
                    "onclick"   => "delete_row(this)",
                    "x-token"   => csrf_token(),
                    "data-url"  => route($this->route . ".destroy", $data->jaspel_id),
                ]);
            }
            return $button;
        })->editColumn("jaspel_bulan",function($data){
            $jaspelBulan = get_namaBulan($data->jaspel_bulan).' '.$data->jaspel_tahun;
            return $jaspelBulan;
        })->addColumn('penjamin', function ($data) {
            $repo = DB::table('repository_download')->where("id",$data->repo_id)->get();
            $groupPenjamin=[];
            foreach ($repo as $key => $value) {
                $penjamin = ["ALL"];
                if (!empty($value->group_penjamin)) {
                    $penjamin = json_decode($value->group_penjamin,true);
                    $penjamin = DB::table("ms_reff")->whereIn("reff_code",$penjamin)->where('reffcat_id',5)->pluck("reff_name")->toArray();
                }
                $groupPenjamin[$key] = implode("<br>",$penjamin);
            }
            $groupPenjamin = implode('<br>',array_unique($groupPenjamin,SORT_REGULAR));
            $groupPenjamin .= "
            <br>
            <small>$data->keterangan</small>";
            return $groupPenjamin;
        })->rawColumns(['action','penjamin']);
        return $datatables->make(true);
    }

    public function cetak_laporan(Request $request)
    {
        if ($request->jenis_report == 1) {
            return $this->print_pdf($request->jaspel_id);
        }elseif ($request->jenis_report == 2) {
            return $this->print_struktural($request->jaspel_id);
        }elseif ($request->jenis_report == 3) {
            return $this->print_medis($request->jaspel_id);
        }
    }

    public function export_excel($id)
	{
		return Excel::download(new JaspelExport($id), 'jaspel.xlsx');
	}

    public function print_pdf($id)
    {
        $data['header'] = Jasa_pelayanan::from("jasa_pelayanan as jp")
                ->where([
                    "jp.jaspel_id"      => $id
                ])
                ->join("jasa_pelayanan_detail as jd","jp.jaspel_id","=","jd.jaspel_id")
                ->join("komponen_jasa as kj","kj.komponen_id","=","jd.komponen_id")
                ->orderBy("kj.komponen_kode","ASC")
                ->get();
        $data['detail'] = DB::select("SELECT x.kode_komponen,x.nama_komponen,json_arrayagg(
            json_object('nomor_rekening',x.nomor_rekening,'nip',x.emp_no, 'nama', x.emp_name,'unit',x.unit_name,'skor',x.skor,'nominal',x.nominal_terima)
        )detail
        FROM (
            SELECT ks.kode_komponen,ks.nama_komponen,e.nomor_rekening,e.emp_no,e.emp_name,mu.unit_name,jm.*
            FROM jp_byname_medis jm
            join employee e on e.emp_id = jm.emp_id
            JOIN ms_unit mu ON e.unit_id_kerja = mu.unit_id
            JOIN komponen_jasa_sistem ks ON ks.id = jm.komponen_id
            where jm.jaspel_id = '$id'
        )x
        GROUP BY x.nama_komponen,x.kode_komponen
        ORDER BY x.kode_komponen");
        return view("jasa_pelayanan.printout.print_jaspel",compact('data'));
        // $pdf = PDF::loadview("jasa_pelayanan.printout.print_jaspel",compact('data'));
        // return $pdf->download('laporan-pegawai.pdf');
        // return $pdf->stream();
        
    }

    public function print_struktural($id)
    {
        $data['data'] = DB::select("
            SELECT e.emp_no,e.emp_name,mu.unit_name,
            json_arrayagg(json_object('id',ks.id,'komponen',ks.nama_komponen,'skor',jm.skor,'nominal',jm.nominal_terima))detail
            FROM jp_byname_medis jm
            join employee e on e.emp_id = jm.emp_id
            JOIN ms_unit mu ON e.unit_id_kerja = mu.unit_id
            JOIN komponen_jasa_sistem ks ON ks.id = jm.komponen_id
            where jm.jaspel_id = '$id' and jm.komponen_id in (3,4,5)
            GROUP BY e.emp_no,e.emp_name,mu.unit_name
        ");
        $data['header']  = Komponen_jasa_sistem::whereIn('id',[3,4,5])->get();
        return view("jasa_pelayanan.printout.print_struktural",compact('data'));
        
    }

    public function print_medis($id)
    {
        $data = DB::select("
            SELECT e.emp_no,e.emp_name,mu.unit_name,
            json_arrayagg(json_object('id',ks.id,'komponen',ks.nama_komponen,'skor',jm.skor,'nominal',jm.nominal_terima))detail
            FROM komponen_jasa_sistem ks
            left JOIN jp_byname_medis jm ON ks.id = jm.komponen_id
            left join employee e on e.emp_id = jm.emp_id
            left JOIN ms_unit mu ON e.unit_id_kerja = mu.unit_id
            where jm.jaspel_id = '$id' AND e.is_medis = 't' and jm.komponen_id in (7,8,9)
            GROUP BY e.emp_no,e.emp_name,mu.unit_name
            ORDER BY e.emp_name
        ");
        $header = Komponen_jasa_sistem::whereIn('id',[7,8,9])->get();
        return view("jasa_pelayanan.printout.print_medis",compact('data','header'));
        
    }

    public function get_dataTableEmployee(Request $request)
    {
        $data = Employee::from("employee as e")
                ->join("ms_unit as mu","mu.unit_id","=","e.unit_id_kerja")
                ->select([
                    'emp_id',
                    'emp_no',
                    'emp_name',
                    'unit_id_kerja',
                    'unit_name'
                ]);
        if ($request->jabatan_type) {
            $data->where("jabatan_type",$request->jabatan_type);
        }
        
        if ($request->is_medis) {
            $data->where("is_medis",$request->is_medis);
        }

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('checkbox', function ($data) {
            $button = '<input type="checkbox" name="emp_id[]" value="'.$data->emp_id.'" />';
            return $button;
        })->rawColumns(['checkbox']);
        return $datatables->make(true);
    }

    public function create()
    {
        $jasa_pelayanan = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('jasa_pelayanan'));
    }

    public function store(Request $request)
    {
        $input      = Cache::get('cacheInputJasa');
        $jasaHeader = Cache::get('cacheJasaHeader');
        $allJasa    = Cache::get("cacheJasaMerger");
        list($bulan,$tahun) = explode('-',$input['jaspel_bulan']);
        $nomor = Servant::generate_code_transaksi([
            "text"	=> "INSENT/NOMOR/".date("d.m.Y"),
            "table"	=> "jasa_pelayanan",
            "column"	=> "no_jasa",
            "delimiterFirst" => "/",
            "delimiterLast" => "/",
            "limit"	=> "2",
            "number"	=> "-1",
        ]);
        $dataJasa = [
            'tanggal_jaspel'        =>  date_db($input['tanggal_jaspel']),
            'jaspel_bulan'          =>  $bulan,
            'jaspel_tahun'          =>  $tahun,
            'nominal_pendapatan'    =>  $input['nominal_pendapatan'],
            'percentase_jaspel'     =>  $input['percentase_jaspel'],
            'nominal_jaspel'        =>  $input['nominal_pembagian'],
            'created_by'            =>  Auth::user()->id,
            'status'                =>  '1',
            'keterangan'            =>  ($input['keterangan']??null),
            'no_jasa'               =>  $nomor,
            "repo_id"               => $input["repo_id"]
        ];

        //check sudah pernah disimpan
        $saved = Jasa_pelayanan::where([
            'tanggal_jaspel'        =>  date_db($input['tanggal_jaspel']),
            'jaspel_bulan'          =>  $bulan,
            'jaspel_tahun'          =>  $tahun,
            'nominal_pendapatan'    =>  $input['nominal_pendapatan'],
            'percentase_jaspel'     =>  $input['percentase_jaspel'],
            'nominal_jaspel'        =>  $input['nominal_pembagian'],
            "repo_id"               => $input["repo_id"]
        ])->first();
        if ($saved) {
            return response()->json([
                'success' => false,
                'message' => "Jasa pelayanan sudah disimpan. silahkan checkout proporsi jasa!"
            ]);
        }

        $valid = $this->form_validasi($dataJasa);
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $valid['message']
            ]);
        }
        DB::beginTransaction();
        try {
            $valid['data']['tanggal_jaspel'] = date("Y-m-d",strtotime($input['tanggal_jaspel']));
            $jaspel=Jasa_pelayanan::create($valid['data']);
            $jaspelId = $jaspel->jaspel_id;
            $jaspelDetail = [];
            foreach ($jasaHeader as $key => $value) {
                $jaspelDetail = [
                    "jaspel_id"       => $jaspelId,
                    "komponen_id"     => $value["komponen_id"],
                    "percentase"      => $value["percentase"],
                    "nominal"         => $value["nominal"],
                ];
                $parentId = DB::table('jasa_pelayanan_detail')->insertGetId($jaspelDetail);
                if (!empty($value['detail'])) {
                    foreach ($value['detail'] as $x => $rs) {
                        $jaspelDetail = [
                            "jaspel_id"       => $jaspelId,
                            "komponen_id"     => $rs["komponen_id"],
                            "percentase"      => $rs["percentase"],
                            "nominal"         => $rs["nominal"],
                            "jaspeldetail_parent"   => $parentId
                        ];
                        DB::table('jasa_pelayanan_detail')->insert($jaspelDetail);
                    }
                }
            }

            /* foreach ($allJasa as $key => $value) {
                if (empty($value['detail'])) {
                    continue;
                }
                $dataJasa=[];
                foreach ($value['detail'] as $x => $rs) {
                    $dataJasa = [
                        'komponen_id'       =>  $value['komponen_id'],
                        'emp_id'            =>  $rs['emp_id'],
                        'jaspel_id'         =>  $jaspelId,
                        'skor'              =>  $rs['skor'],
                        'nominal_terima'    =>  $rs['jasa']
                    ];
                    $jpMedisId = DB::table('jp_byname_medis')->insertGetId($dataJasa);
                    if (!empty($rs['id_tindakan'])) {
                        $billing_id = json_decode($rs['id_tindakan'],true);
                        DB::table('point_medis')
                        ->whereIn('id',$billing_id)
                        ->whereIn('repo_id',$input['repo_id'])
                        ->update([
                            "jp_medis_id"   => $jpMedisId,
                            "jaspel_id"     => $jaspelId,
                            "is_usage"      => 't'
                        ]);
                    }

                    if (!empty($rs['id_skor'])) {
                        $skor = json_decode($rs['id_skor'],true); 
                        $jasaSkor=[];
                        foreach ($skor as $y => $sk) {
                            $jasaSkor[$y] = [
                                "skor_id"       => $sk,
                                "jp_medis_id"   => $jpMedisId,
                                "jaspel_id"     => $jaspelId
                            ];
                        }
                        Skor_pegawai::whereIn("id",$skor)->update([
                            "prepare_remun"   => "f"
                        ]);
                        DB::table("jasa_by_skoring")->insert($jasaSkor);
                    }
                }
            } */

            DB::table('repository_download')
            ->where('id',$input['repo_id'])
            ->update([
                "is_used"       => 't'
            ]);
            DB::table('proporsi_jasa_individu')
            ->where([
                "is_used"       => "f",
                "jasa_bulan"    => $input['jaspel_bulan']
            ])
            ->update([
                "is_used"       => 't',
                "id_jaspel"     => $jaspelId
            ]);

            DB::commit();
            $resp = [
                'success'   => true,
                'message'   => 'Data Berhasil Disimpan!',
                'response'  => [
                    "jaspel_id"     => $jaspelId
                ]
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            $resp = [
                'success' => false,
                'message' => 'Data Gagal Disimpan! <br>' . $e->getMessage()." line ".$e->getLine()
            ];
        }
        return response()->json($resp);
    }

    public function simpan_per_proporsi($jaspelId,$komponen_id)
    {
        $allJasa    = Cache::get("cacheJasaMerger");
        $allJasa = array_filter($allJasa, function ($var) use($komponen_id) {
            return ($var['komponen_id'] == $komponen_id);
        });

        if (empty($allJasa)) {
            return response()->json([
                "code"      => 202,
                "message"   => "Data tidak ditemukan"
            ]);
        }
        DB::beginTransaction();
        try {
            DB::table('jp_byname_medis')->where([
                "komponen_id"   => $komponen_id,
                "jaspel_id"     => $jaspelId
            ])->delete();

            foreach ($allJasa as $key => $value) {
                if (empty($value['detail'])) {
                    continue;
                }
                $dataJasa=[];
                foreach ($value['detail'] as $x => $rs) {
                    $dataJasa = [
                        'komponen_id'       =>  $value['komponen_id'],
                        'emp_id'            =>  $rs['emp_id'],
                        'jaspel_id'         =>  $jaspelId,
                        'skor'              =>  $rs['skor'],
                        'nominal_terima'    =>  $rs['jasa']
                    ];
                    $jpMedisId = DB::table('jp_byname_medis')->insertGetId($dataJasa);
                    if (!empty($rs['id_tindakan'])) {
                        $billing_id = json_decode($rs['id_tindakan'],true);
                        DB::table('point_medis')
                        ->whereIn('id',$billing_id)
                        ->update([
                            "jp_medis_id"   => $jpMedisId,
                            "jaspel_id"     => $jaspelId,
                            "is_usage"      => 't'
                        ]);
                    }

                    if (!empty($rs['id_skor'])) {
                        $skor = json_decode($rs['id_skor'],true); 
                        $jasaSkor=[];
                        foreach ($skor as $y => $sk) {
                            $jasaSkor[$y] = [
                                "skor_id"       => $sk,
                                "jp_medis_id"   => $jpMedisId,
                                "jaspel_id"     => $jaspelId
                            ];
                        }
                        Skor_pegawai::whereIn("id",$skor)->update([
                            "prepare_remun"   => "f"
                        ]);
                        DB::table("jasa_by_skoring")->insert($jasaSkor);
                    }
                }
            }
            DB::commit();
            $resp = [
                'code'      => 200,
                'message'   => 'Proposi berhasil dicheckout'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            $resp = [
                'code' => 201,
                'message' => 'Data Gagal Disimpan! <br>' . $e->getMessage()." line ".$e->getLine()
            ];
        }
        return response()->json($resp);
    }

    public function finish_jaspel(Request $request)
    {
        $input      = Cache::get('cacheInputJasa');
        $repo       = Repository_download::find($input["repo_id"]);

        //skor pegawai
        Skor_pegawai::where("bulan_update",$repo->bulan_pelayanan)->update([
            "prepare_remun"             => "f",
            "prepare_remun_month"       => null,
        ]);

        cache::forget('cacheInputJasa');
        cache::forget('cacheJasaHeader');
        cache::forget('cacheJasaProporsi');
        cache::forget('cacheJasaMerger');
        Jasa_pelayanan::findOrFail($request->jaspel_id)->update([
            "status"    => 2
        ]);

        return response()->json([
            "code"      => 200,
            "message"   => "OK"
        ]);
    }

    public function hitung_jasa(Request $request)
    {
        cache::forget('cacheInputJasa');
        cache::forget('cacheJasaHeader');
        cache::forget('cacheJasaProporsi');
        cache::forget('cacheJasaMerger');
        try {
            $komponen = Komponen_jasa::where("komponen_parent","0")->get();
            $jasaHeader=[];
            Cache::add('cacheInputJasa',$request->all(),3000);
            foreach ($komponen as $key => $value) {
                $jasa = $request->nominal_pembagian*($value->komponen_percentase/100);
                $jasaHeader[$key] = [
                    "komponen_id"   => $value->komponen_id,
                    "komponen"      => $value->komponen_nama,
                    "percentase"    => $value->komponen_percentase,
                    "nominal"       => $jasa
                ];
                if ($value->komponen_id != 4) {
                    if ($value->has_child == 't') {
                        $komponenChild = Komponen_jasa::where("komponen_parent",$value->komponen_id)->get();
                        $detail = [];
                        foreach ($komponenChild as $x => $c) {
                            $jasaChild = $jasa*($c->komponen_percentase/100);
                            $detail[$x] = [
                                "komponen_id"  => $c->komponen_id,
                                "komponen"     => $c->komponen_nama,
                                "percentase"   => $c->komponen_percentase,
                                "nominal"      => $jasaChild
                            ];
                        }
                        $jasaHeader[$key]['detail']     = $detail;
                    }
                }else{
                    $jasaHeader[$key]['detail'] = $this->hitung_eksekutif($jasa,$request);
                }
            }
            Cache::add('cacheJasaHeader',$jasaHeader,3000);
            //hitung per proporsi
            $byname = $this->hitung_penerima_by_skor($request);
            Cache::add('cacheJasaProporsi',$byname,3000);
            $resp = [
                'success' => true,
                'message' => 'Perhitungan Jasa Berhasil!'
            ];
        } catch (\Exception $e) {
            $resp = [
                'success' => false,
                'message' => 'Gagal Hitung Jasa! <br>' . $e->getMessage(). $e->getLine()
            ];
        }
        return response()->json($resp);
    }

    public function hitung_penerima_by_skor($request)
    {
        $proporsi = Komponen_jasa_sistem::all();
        $repoDownload = Repository_download::find($request->repo_id);
        $dataJasa = [];
        $totalMedis=0;
        foreach ($proporsi as $key => $value) {
            $jasaAsal = $request->nominal_pembagian*$value->percentase_jasa;
            $dataJasa[$key]['komponen_id'] = $value->id;
            $dataJasa[$key]['komponen_nama'] = $value->nama_komponen;
            if ($value->for_medis == 't') {
                $eksekutif = Cache::get("cacheEksekutif");
                if (!empty($eksekutif)) {
                    $totalMedis = Komponen_jasa_sistem::where('for_medis','t')->sum('percentase_jasa');
                    $totalMedis = $totalMedis*$request->nominal_pembagian;
                    $jasa1    = ($request->nominal_pembagian*$value->percentase_jasa);
                    $persentaseEks = ($jasa1/$totalMedis) * $eksekutif['total_jasa'];
                    $jasaAsal = $jasa1 - $persentaseEks;
                }
            }
            if ($value->type_jasa == 1) {
                $data = Proporsi_jasa_individu::from("proporsi_jasa_individu as pi")
                        ->join("employee as e","e.emp_id","=","pi.employee_id")
                        ->where([
                            "pi.komponen_id"       => $value->id,
                            "pi.jasa_bulan"     => $request->jaspel_bulan,
                            "pi.is_used"        => 'f',
                        ]);
                $penerima = $data->count();
                $dataJasa[$key]["total_skor"] = $penerima;
                $totalJasa=0;
                foreach ($data->get() as $x => $v) {
                    $jasa = $jasaAsal/$penerima;
                    $dataJasa[$key]['detail'][$x] = [
                        "emp_id"    => $v->emp_id,
                        "nip"       => $v->emp_nip,
                        "name"      => $v->emp_name,
                        "skor"      => 1,
                        "jasa"      => $jasa
                    ];
                    $totalJasa += $jasa;
                }
                $dataJasa[$key]['total_jasa'] = $totalJasa;
            }elseif ($value->type_jasa == 2) {
                $totalSkor = 0;
                $totalJasa = 0;
                $details = [];
                // Menghitung total skor sekaligus mengambil data dengan chunk
                DB::table('point_medis as pm')
                ->join('employee as e', 'e.emp_id', '=', 'pm.employee_id')
                ->join('proporsi_jasa_individu as pi', 'e.emp_id', '=', 'pi.employee_id')
                ->groupBy('e.emp_no', 'e.emp_id', 'e.emp_name')
                ->select([
                    'e.emp_id',
                    'e.emp_nip',
                    'e.emp_no',
                    'e.emp_name',
                    DB::raw('SUM(pm.skor / 10000) AS total_skor'),
                    DB::raw('json_arrayagg(pm.id) AS id_tindakan')
                ])
                ->whereRaw("(is_eksekutif = '0' OR (is_eksekutif = '1' AND jenis_tagihan = '2'))")
                ->where([
                    'repo_id' => $request->repo_id,
                    'pi.komponen_id' => $value->id,
                    'pm.is_usage' => 'f',
                    'pi.is_used' => 'f',
                    'pi.jasa_bulan' => $request->jaspel_bulan
                ])
                ->orderBy('e.emp_id')
                ->chunk(1000, function ($data) use (&$totalSkor, &$totalJasa, &$details, $jasaAsal) {
                    $chunkTotalSkor = $data->sum('total_skor');
                    $totalSkor += $chunkTotalSkor;

                    $data->each(function ($item) use (&$totalJasa, &$details, $chunkTotalSkor, $jasaAsal, &$totalSkor) {
                        $jasa = ($item->total_skor / $totalSkor) * $jasaAsal;
                        $totalJasa += $jasa;
                        $details[] = [
                            'emp_id' => $item->emp_id,
                            'nip' => $item->emp_nip,
                            'name' => $item->emp_name,
                            'skor' => $item->total_skor,
                            'jasa' => $jasa,
                            'id_tindakan' => $item->id_tindakan
                        ];
                    });
                });

                $dataJasa[$key]['total_skor'] = $totalSkor;
                $dataJasa[$key]['detail'] = $details;
                $dataJasa[$key]['total_jasa'] = $totalJasa;
            }elseif ($value->type_jasa == 3) {
                $data = Skor_pegawai::from("skor_pegawai as sp")
                        ->join("employee as e","e.emp_id","=","sp.emp_id")
                        ->join("proporsi_jasa_individu as pi","e.emp_id","=","pi.employee_id")
                        ->groupBy(["e.emp_nip","e.emp_name","e.emp_id"])
                        ->select(["e.emp_id","e.emp_nip","e.emp_name",DB::raw('SUM(coalesce(sp.skor_koreksi,sp.total_skor)) AS total_skor'),DB::raw('json_arrayagg(sp.id) AS id_skor')])
                        ->where([
                            "prepare_remun_month"   => $request->jaspel_bulan,
                            "pi.jasa_bulan"         => $request->jaspel_bulan,
                            "sp.bulan_update"       => $repoDownload->bulan_pelayanan,
                            "pi.is_used"            => 'f',
                            "pi.komponen_id"        => $value->id,
                            // "prepare_remun"         => "t",
                            "is_medis"              => ''.($value->for_medis??'f').'',
                        ]);
                $dataArray=$data->get()->toArray();
                $totalSkor=array_sum(array_column($dataArray,'total_skor'));
                $dataJasa[$key]['total_skor'] = $totalSkor;
                $totalJasa=0;
                foreach ($data->get() as $x => $v) {
                    $jasa = $v->total_skor/$totalSkor*$jasaAsal;
                    $totalJasa += $jasa;
                    $dataJasa[$key]['detail'][$x] = [
                        "emp_id"    => $v->emp_id,
                        "nip"       => $v->emp_nip,
                        "name"      => $v->emp_name,
                        "skor"      => $v->total_skor,
                        "jasa"      => $jasa,
                        "id_skor"   => $v->id_skor
                    ];
                }
                $dataJasa[$key]['total_jasa'] = $totalJasa;
            }
        }
        return $dataJasa;
    }

    public function hitung_eksekutif($jasaAsal,$request)
    {
        $jasaHeader=[];
        Cache::forget('cacheEksekutif');
        //hitung pelayanan eksekutif
        $data = DB::table("point_medis as pm")
                ->join("employee as e","e.emp_id","=","pm.employee_id")
                ->join("proporsi_jasa_individu as pi","e.emp_id","=","pi.employee_id")
                ->groupBy(["e.emp_no","e.emp_id","e.emp_name"])
                ->select(["e.emp_id","e.emp_no","e.emp_name",DB::raw('SUM(pm.skor) AS total_skor'),DB::raw('json_arrayagg(pm.id) AS id_tindakan')])
                ->where("repo_id",$request->repo_id)
                ->where([
                    "is_eksekutif"          => "1",
                    "is_usage"              => "f",
                    "jenis_tagihan"         => "1",
                    "pi.komponen_id"        => 9,
                    "pi.is_used"            => "f",
                    "pi.jasa_bulan"         => $request->jaspel_bulan
                ]);
        $dataEksekutif  = [];
        $totalEksekutif=0;
        foreach ($data->get() as $key => $value) {
            $dataEksekutif['detail'][$key] = [
                "emp_id"    => $value->emp_id,
                "nip"       => $value->emp_no,
                "name"      => $value->emp_name,
                "skor"      => $value->total_skor,
                "jasa"      => $value->total_skor,
                "id_tindakan"   => $value->id_tindakan
            ];
            $totalEksekutif += $value->total_skor;
        }
        $dataEksekutif['komponen_id'] = '9';
        $dataEksekutif['komponen_nama'] = 'MEDIS EKSEKUTIF';
        $dataEksekutif['total_jasa'] = $totalEksekutif;
        Cache::add("cacheEksekutif",$dataEksekutif);
        $komponen=Komponen_jasa::findOrFail(5);
        $jasaHeader[0] = [
            "komponen_id"   => 5,
            "komponen"      => $komponen->komponen_nama,
            "percentase"    => $komponen->komponen_percentase,
            "nominal"       => $totalEksekutif
        ];
        $jasaAsal = ($jasaAsal - $totalEksekutif);
        $komponen=Komponen_jasa::findOrFail(6);
        $jasaHeader[1] = [
            "komponen_id"   => 6,
            "komponen"      => $komponen->komponen_nama,
            "percentase"    => $komponen->komponen_percentase,
            "nominal"       => ($jasaAsal*$komponen->komponen_percentase/100)
        ];
        $komponen=Komponen_jasa::findOrFail(7);
        $jasaHeader[2] = [
            "komponen_id"   => 7,
            "komponen"      => $komponen->komponen_nama,
            "percentase"    => $komponen->komponen_percentase,
            "nominal"       => ($jasaAsal*$komponen->komponen_percentase/100),
        ];
        return $jasaHeader;
    }

    public function hasil_perhitungan()
    {
        return $this->themes($this->folder . '.hasil_perhitungan', null, "Hasil Sementara Perhitungan Jasa");
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

    public function edit(Jasa_pelayanan $jasa_pelayanan)
    {
        return view($this->folder . '.form', compact('jasa_pelayanan'));
    }
    public function update(Request $request, Jasa_pelayanan $jasa_pelayanan)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = Jasa_pelayanan::findOrFail($jasa_pelayanan->jaspel_id);
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
        $data = Jasa_pelayanan::findOrFail($id);
        DB::beginTransaction();
        try {
            DB::table("repository_download")->where("id",$data->repo_id)->update([
                "is_used"   => "f"
            ]);

            DB::table("proporsi_jasa_individu")->where("id_jaspel",$id)
            ->update([
                "is_used"   => "f",
                "id_jaspel" => null
            ]);

            DB::table("point_medis")->where("jaspel_id",$id)
            ->update([
                "jp_medis_id"   => null,
                "jaspel_id"     => null,
                "is_usage"      => 'f'
            ]);

            $dataSkor = DB::table("jasa_by_skoring")->where("jaspel_id",$id);
            foreach ($dataSkor->get() as $key => $value) {
                DB::table("skor_pegawai")->where("id",$value->skor_id)
                ->update([
                    "prepare_remun"         => "t",
                    "prepare_remun_month"   => $data->jaspel_bulan."-".$data->jaspel_tahun,
                ]);
            }
            DB::table("jp_byname_medis")->where("jaspel_id",$id)->delete();
            DB::table("jasa_pelayanan_detail")->where("jaspel_id",$id)->delete();

            $data->delete();
            DB::commit();
            $resp = [
                'success' => true,
                'message' => 'Data Berhasil Dihapus!'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            $resp = [
                'success' => false,
                'message' => 'Data Gagal Dihapus! <br>' . $e->getMessage()
            ];
        }
        return response()->json($resp);
    }

    public function remove_jaspel($id)
    {
        $data = Jasa_pelayanan::findOrFail($id);
        DB::beginTransaction();
        try {
            $repoDownload = DB::table("repository_download")->where("id",$data->repo_id);
            $repoDownload->update([
                "is_used"       => "f"
            ]);

            DB::table("proporsi_jasa_individu")->where("id_jaspel",$id)
            ->update([
                "is_used"   => "f",
                "id_jaspel" => null
            ]);

            DB::table("point_medis")->where("jaspel_id",$id)
            ->update([
                "jp_medis_id"   => null,
                "jaspel_id"     => null,
                "is_usage"      => 'f'
            ]);

            $dataSkor = DB::table("jasa_by_skoring")->where("jaspel_id",$id);
            foreach ($dataSkor->get() as $key => $value) {
                DB::table("skor_pegawai")->where("id",$value->skor_id)
                ->update([
                    "prepare_remun"         => "t",
                    "prepare_remun_month"   => $data->jaspel_bulan."-".$data->jaspel_tahun,
                ]);
            }
            $data->delete();
            DB::commit();
            $resp = [
                'success' => true,
                'message' => 'Perhitungan berhasil dibatalkan!'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            $resp = [
                'success' => false,
                'message' => 'Data gagl dibatalkan! <br>' . $e->getMessage()
            ];
        }
        return response()->json($resp);
    }
}
