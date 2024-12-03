<?php

namespace App\Http\Controllers;

use App\Libraries\Qontak;
use App\Libraries\Servant;
use App\Models\Detail_indikator;
use App\Models\Diklat;
use App\Models\Employee;
use App\Models\Employee_off;
use App\Models\Log_messager;
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

    public function send_to_verifikator(Request $request)
    {
        $dataUnit = Ms_unit::from("ms_unit as mu")
                    ->join("employee as e","e.emp_id","=","mu.ka_unit")
                    ->select('e.emp_name,e.phone,e.emp_id', DB::raw('GROUP_CONCAT(mu.unit_name) as unit_kerja'))
                    ->groupBy('e.emp_name,e.phone,e.emp_id')
                    ->get();
        $sentMessage=$failedMessage=0;
        foreach ($dataUnit as $key => $value) {
            $waInfo = Qontak::sendInfoSkor($value->phone,$value->emp_name,[
                "penerima"  => $value->emp_name,
                "unit"      => $value->unit_kerja
            ]);
            
            if ($waInfo["status"] == "success") {
                $sentMessage++;
            }else {
                $failedMessage++;
            }
        }
        return response()->json([
            "code"      => 200,
            "message"   => "Terkirim : $sentMessage. Gagal : $failedMessage"
        ]);
    }

    public function get_data()
    {
        return view($this->folder . '.data');
    }

    public function hasil_skor()
    {
        return $this->themes($this->folder . '.hasil_skor', null, 'Hasil Perhitungan Skor Individu');
    }

    /* public function save_skor()
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
    } */

    public function save_skor()
    {
        $skor = Cache::get('skorPegawai');
        // Array untuk menyimpan data skor pegawai dan detail skor pegawai
        $insertSkorPegawai = [];
        $insertDetailSkorPegawai = [];

        // Array untuk menyimpan data yang akan dihapus dari tabel Skor_pegawai
        $deletedEmpIds = [];

        // Memproses data skor untuk disimpan
        array_map(function ($value) use (&$insertSkorPegawai, &$insertDetailSkorPegawai, &$deletedEmpIds) {
            $skoring = [
                'basic_index'     => $value['dataSkor']['basic']['skor'],
                'capacity_index'  => ($value['dataSkor']['capacity']['skor'] + $value['dataSkor']['certificate']['skor']),
                'emergency_index' => $value['dataSkor']['emergency']['skor'],
                'unit_risk_index' => $value['dataSkor']['risk']['skor'],
                'position_index'  => ($value['dataSkor']['position']['skor'] + $value['dataSkor']['tugas']['skor']),
                'competency_index'=> $value['dataSkor']['performa']['skor'],
                'total_skor'      => $value['totalSkor'],
                'skor_note'       => $value['skor_note'],
                'bulan_update'    => $value['bulan_update'],
                'emp_id'          => $value['id'],
                'created_by'      => Auth::user()->id,
                'skor_type'       => 1
            ];

            // Menyiapkan data yang akan dihapus dari Skor_pegawai
            $deletedEmpIds[] = [
                "emp_id"        => $skoring["emp_id"],
                "bulan_update"  => $skoring["bulan_update"]
            ];

            // Memasukkan data untuk Detail_skor_pegawai ke dalam array
            foreach ($value['dataSkor'] as $key => $rs) {
                $insertDetailSkorPegawai[] = [
                    "emp_id"        => $value['id'],
                    "kode_skor"     => $key,
                    "detail_skor"   => $rs['keterangan'],
                    "skor"          => $rs['skor']
                ];
            }

            // Memasukkan data untuk Skor_pegawai ke dalam array
            $insertSkorPegawai[] = $skoring;
        }, $skor);

        // Transaksi database dimulai
        DB::beginTransaction();

        try {
            // Menghapus data dari Skor_pegawai yang sudah ada
            foreach ($deletedEmpIds as $deleteEmpId) {
                Skor_pegawai::where($deleteEmpId)->delete();
            }

            // Memasukkan data baru ke dalam Skor_pegawai dengan batch insert
            Skor_pegawai::insert($insertSkorPegawai);

            // Mengambil id terakhir yang dimasukkan ke dalam Skor_pegawai
            $lastIds = Skor_pegawai::whereIn('emp_id', array_column($skor, 'id'))
                ->whereIn('bulan_update', array_column($skor, 'bulan_update'))
                ->pluck('id', 'emp_id');

            // Menyiapkan data untuk diinsert ke dalam Detail_skor_pegawai
            $insertDetailData = array_map(function ($detailSkor) use ($lastIds) {
                $detailSkor['skor_id'] = $lastIds[$detailSkor['emp_id']];
                return $detailSkor;
            }, $insertDetailSkorPegawai);

            // Memasukkan data ke dalam Detail_skor_pegawai dengan batch insert
            DB::table('detail_skor_pegawai')->insert($insertDetailData);

            // Commit transaksi jika berhasil
            DB::commit();

            $resp = [
                'success' => true,
                'message' => 'Data Berhasil Disimpan!'
            ];
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollback();

            $resp = [
                'success' => false,
                'message' => 'Data gagal disimpan! <br>'.$e->getMessage()
            ];
        }

        // Mengembalikan response JSON
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

    public function generate_skorx(Request $request)
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
                /* $pegawai =  Employee::from("employee as e")
                            ->where("e.emp_active","t")
                            ->join("ms_unit as mu","mu.unit_id","=","e.unit_id_kerja")
                            ->join("detail_indikator as di","di.detail_id","=","e.pendidikan")
                            ->join("indikator as i","i.id","=","di.indikator_id")
                            ->select(["e.*","di.detail_name as pendidikan","di.skor as skor_pendidikan","di.indikator_id","i.bobot","mu.unit_name"])
                            ->get(); */
                $pegawai = Employee::where('emp_active', 't')
                            ->with(['unit', 'pendidikanDetail.indikator'])
                            ->get();
            }

            foreach ($pegawai as $key => $pgw) {
                if (empty($pgw->unit->unit_name)) {
                    continue;
                }
                $data[$key] = [
                    "nip"              => $pgw->emp_no,
                    "id"               => $pgw->emp_id,
                    "is_medis"         => $pgw->is_medis,
                    "unit_kerja"       => $pgw->unit->unit_name ?? '',
                    "bulan_update"     => $request->bulan_skor,
                    "nama"             => $pgw->emp_name
                ];

                $skor=floor(($pgw->gaji_pokok/1000000*1) * 10) / 10;
                $data[$key]['dataSkor']['basic'] = [
                    "skor"          => $skor,
                    "keterangan"    => "BASIC INDEX BERDASARKAN GAJI POKOK"
                ];
                $data[$key]['totalSkor'] = $skor;

                /* $skor=($pgw->skor_pendidikan*$pgw->bobot);
                $data[$key]['totalSkor'] = $data[$key]['totalSkor'] +$skor;
                $data[$key]['dataSkor']['capacity'] = [
                    "skor"          => $skor,
                    "keterangan"    => "BASIC INDEX PENDIDIKAN - ".$pgw->pendidikan
                ]; */

                if ($pgw->pendidikanDetail) {
                    $skor = ($pgw->pendidikanDetail->skor * $pgw->pendidikanDetail->indikator->bobot);
                    $data[$key]['totalSkor'] += $skor;
                    $data[$key]['dataSkor']['capacity'] = [
                        "skor"          => $skor,
                        "keterangan"    => "BASIC INDEX PENDIDIKAN - " . $pgw->pendidikanDetail->detail_name
                    ];
                }
                // $data[$key]['totalSkor'] = $data[$key]['totalSkor'] + $pgw->last_risk_index;
                $data[$key]['totalSkor'] += $pgw->last_risk_index;
                $data[$key]['dataSkor']['risk'] = [
                    "skor"          => $pgw->last_risk_index,
                    "keterangan"    => "RISK INDEX (INFEKSIUS+ADMINISTRASI) - ".$pgw->unit->unit_name
                ];

                $data[$key]['totalSkor'] += $pgw->last_emergency_index;
                $data[$key]['dataSkor']['emergency'] = [
                    "skor"          => $pgw->last_emergency_index,
                    "keterangan"    => "RISK INDEX (INFEKSIUS+ADMINISTRASI) - " . $pgw->unit->unit_name
                ];

                //get jabatan skor
                $posisiId = $pgw->jabatan_struktural ?? $pgw->jabatan_fungsional ?? 30;
                $posisi = Cache::remember("position_{$posisiId}", 3000, function () use ($posisiId) {
                    return Detail_indikator::from("detail_indikator as di")
                        ->where("detail_id", $posisiId)
                        ->join("indikator as i", "i.id", "=", "di.indikator_id")
                        ->first();
                });

                if (!$posisi) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Jabatan pegawai : ' . $pgw->emp_name . ' tidak terdaftar'
                    ]);
                }

                $data[$key]['totalSkor'] += $pgw->last_position_index;
                $data[$key]['dataSkor']['position'] = [
                    "skor"          => $pgw->last_position_index,
                    "keterangan"    => "POSITION INDEX - " . $posisi->detail_name
                ];
                
            }
            Cache::add('skorPegawai',array_values($data),3000);
            $emp_ids = $pegawai->pluck('emp_id')->toArray();
            Cache::remember('skorError', 3000, function () use ($emp_ids){
                $emp_id_str = implode(',', $emp_ids);
                $dataError = DB::select("
                            select e.emp_no,e.emp_name,mu.unit_name from employee e 
                            join ms_unit mu on mu.unit_id = e.unit_id_kerja
                            where e.emp_active = 't' and e.emp_id not in (".$emp_id_str.")
                        ");
                return $dataError;
            });

            $resp = [
                'success' => true,
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

    public function generate_skor(Request $request)
    {
        try {
            $pegawai = Employee::where('emp_active', 't')
                ->when($request->unit_id, function ($query, $unit_id) {
                    return $query->where('unit_id_kerja', $unit_id);
                })
                ->when($request->emp_id_gen, function ($query, $emp_id) {
                    return $query->where('emp_id', $emp_id);
                })
                ->with([
                    'unit',
                    'pendidikanDetail.indikator',
                    'tugasTambahan' => function ($query) {
                        $query->whereDate('tanggal_akhir', '>=', now())
                            ->with('detailIndikator.indikator');
                    },
                    'performaIndex' => function ($query) {
                        $query->whereDate('expired_date', '>=', now())
                            ->with(['detailIndikator.indikator','detailPerform']);
                    },
                    'diklat' => function ($query) {
                        $query->whereNotNull('indikator_skor')
                            ->with('detailIndikator.indikator');
                    },
                ])
                ->get();

            // Proses data skor untuk setiap pegawai
            $data = $pegawai->map(function ($pgw) use ($request) {
                if (empty($pgw->unit->unit_name)) {
                    return null;
                }

                $totalSkor = 0;
                $dataSkor = [];

                // Skor Basic Index berdasarkan gaji pokok
                $basicSkor = floor(($pgw->gaji_pokok / 1000000) * 10) / 10;
                $dataSkor['basic'] = [
                    "skor"       => $basicSkor,
                    "keterangan" => "BASIC INDEX BERDASARKAN GAJI POKOK"
                ];
                $totalSkor += $basicSkor;

                // Skor Capacity Index berdasarkan pendidikan
                if ($pgw->pendidikanDetail) {
                    $pendidikanSkor = $pgw->pendidikanDetail->skor * $pgw->pendidikanDetail->indikator->bobot;
                    $dataSkor['capacity'] = [
                        "skor"       => $pendidikanSkor,
                        "keterangan" => "BASIC INDEX PENDIDIKAN - " . $pgw->pendidikanDetail->detail_name
                    ];
                    $totalSkor += $pendidikanSkor;
                }else {
                    $dataSkor['capacity'] = [
                        "skor"       => 0,
                        "keterangan" => "BASIC INDEX PENDIDIKAN"
                    ];
                }

                // Skor Risk Index (Infeksius + Administrasi)
                $dataSkor['risk'] = [
                    "skor"       => $pgw->last_risk_index,
                    "keterangan" => "RISK INDEX (INFEKSIUS+ADMINISTRASI) - " . $pgw->unit->unit_name
                ];
                $totalSkor += $pgw->last_risk_index;

                // Skor Emergency Index
                $dataSkor['emergency'] = [
                    "skor"       => $pgw->last_emergency_index,
                    "keterangan" => "EMERGENCY INDEX - " . $pgw->unit->unit_name
                ];
                $totalSkor += $pgw->last_emergency_index;

                // Skor Position Index berdasarkan jabatan
                $posisiId = $pgw->jabatan_struktural ?? $pgw->jabatan_fungsional ?? 30;
                $posisi = Cache::remember("position_{$posisiId}", 3000, function () use ($posisiId) {
                    return Detail_indikator::from("detail_indikator as di")
                        ->where("detail_id", $posisiId)
                        ->join("indikator as i", "i.id", "=", "di.indikator_id")
                        ->first();
                });

                if (!$posisi) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Jabatan pegawai : ' . $pgw->emp_name . ' tidak terdaftar'
                    ]);
                }

                $dataSkor['position'] = [
                    "skor"       => $pgw->last_position_index,
                    "keterangan" => "POSITION INDEX - " . $posisi->detail_name
                ];
                $totalSkor += $pgw->last_position_index;

                // Skor Diklat & Sertifikat Index
                $sertifikatSkor = $pgw->diklat->sum(function ($d) {
                    return $d->detailIndikator->skor * $d->detailIndikator->indikator->bobot;
                });
                $sertifikatSkor = min($sertifikatSkor, 0.8);
                $diklatNote = $pgw->diklat->pluck("judul_pelatihan")->implode(",");
                $dataSkor['certificate'] = [
                    "skor"       => $sertifikatSkor,
                    "keterangan" => ($sertifikatSkor > 0) ? "DIKLAT & SERTIFIKAT INDEX ".$diklatNote : null
                ];
                $totalSkor += $sertifikatSkor;
                
                // Skor Tugas Tambahan Index
                $tugasSkor = $pgw->tugasTambahan->sum(function ($tugas) use ($pgw) {
                    $skor = $tugas->detailIndikator->skor*$tugas->detailIndikator->indikator->bobot;
                    
                    return $skor;
                });
                if ($pgw->is_medis == 'f') {
                    $tugasSkor = min($tugasSkor, 3.5);
                }
                $tugasNote = $pgw->tugasTambahan->pluck('nama_tugas')->implode(', ');
                $dataSkor['tugas'] = [
                    "skor"       => $tugasSkor,
                    "keterangan" => ($tugasSkor > 0) ? "TUGAS TAMBAHAN INDEX - " . $tugasNote : null
                ];
                $totalSkor += $tugasSkor;

                // Skor Performa Index
                $performaSkor = $pgw->performaIndex->sum(function ($performa) {
                    return $performa->detailIndikator->skor * $performa->detailIndikator->indikator->bobot;
                });
                $performaNote = $pgw->performaIndex->pluck('detailPerform.reff_name')->implode(', ');
                $dataSkor['performa'] = [
                    "skor"       => $performaSkor,
                    "keterangan" => ($performaSkor > 0) ? "PERFORMA INDEX - ".$performaNote : null
                ];
                $totalSkor += $performaSkor;

                return [
                    "nip"         => $pgw->emp_no,
                    "id"          => $pgw->emp_id,
                    "is_medis"    => $pgw->is_medis,
                    "unit_kerja"  => $pgw->unit->unit_name ?? '',
                    "bulan_update"=> $request->bulan_skor,
                    "nama"        => $pgw->emp_name,
                    "dataSkor"    => $dataSkor,
                    "totalSkor"   => $totalSkor,
                    "skor_note"   => "Skor pegawai bulan " . $request->bulan_skor . "."
                ];
            })->filter()->values()->all();

            // dd($data);
            // Simpan hasil skor pegawai ke dalam cache
            Cache::put('skorPegawai', $data, 3000);

            //pegawai tidak dapat skor
            $emp_ids = implode(',',$pegawai->pluck('emp_id')->toArray());
            $dataError = DB::select("
                        select e.emp_no,e.emp_name,mu.unit_name from employee e 
                        join ms_unit mu on mu.unit_id = e.unit_id_kerja
                        where e.emp_active = 't' and e.emp_id not in (".$emp_ids.")
                    ");
            Cache::put('skorError', $dataError, 3000);

            return response()->json([
                "success"    => true,
                "message"    => "Skor Berhasil Digenerate"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success"    => false,
                "message" => "Terjadi kesalahan: " . $e->getMessage()
            ]);
        }
    }

    /* function set_skor($type)
    {
        $skor = Cache::get('skorPegawai');
        $newSkor=[];
        foreach ($skor as $key => $value) {
            if ($type == 1) {
                //get sertifikat skor
                $sertifikat = Diklat::from("diklat as dk")
                ->where("peserta_id", $value["id"])
                    ->join("detail_indikator as di", "di.detail_id", "=", "dk.indikator_skor")
                    ->join("indikator as i", "i.id", "=", "di.indikator_id")
                    ->select([
                        "skor","bobot","judul_pelatihan"
                    ]);
                $sertifikatSkor = 0;
                $sertifikatNote = [];
                if ($sertifikat->count() > 0) {
                    foreach ($sertifikat->get() as $x => $rs) {
                        $sertifikatSkor += ($rs->skor * $rs->bobot);
                        $sertifikatNote[] = $rs->judul_pelatihan;
                    }
                    if ($sertifikatSkor > 0.8) {
                        $sertifikatSkor = 0.8;
                    }
                }
                $sertifikatNote = implode(',', $sertifikatNote);
                $data['certifycate'] = [
                    "skor"          => ($sertifikatSkor),
                    "keterangan"    => ($sertifikatSkor > 0) ? "DIKLAT & SERTIFIKAT INDEX - ($sertifikatNote)" : NULL
                ];
                $value["totalSkor"] = $value['totalSkor'] + $sertifikatSkor;
                
                $value["dataSkor"]  = array_merge($value["dataSkor"],$data);
            }else if ($type == 2) {
                //get tugas tambahan
                $tugasTambahan = Tugas_tambahan::from("tugas_tambahan as tt")
                ->where("emp_id", $value["id"])
                    ->whereRaw("('" . date('Y-m-d') . "' <= tt.tanggal_akhir)")
                    ->join("detail_indikator as di", "di.detail_id", "=", "tt.jabatan_tugas")
                    ->join("indikator as i", "i.id", "=", "di.indikator_id");
                $tugasSkor = 0;
                $tugasNote = [];
                if ($tugasTambahan->count() > 0) {
                    foreach ($tugasTambahan->get() as $x => $rs) {
                        $tugasSkor += ($rs->skor * $rs->bobot);
                        $tugasNote[] = $rs->detail_name . '@' . $rs->nama_tugas;
                    }
                    if ($value["is_medis"] == 'f') {
                        if ($tugasSkor > 3.5) {
                            $tugasSkor = 3.5;
                        }
                    }
                }
                $tugasNote = implode(',', $tugasNote);
                $data['tugas'] = [
                    "skor"          => ($tugasSkor),
                    "keterangan"    => ($tugasSkor > 0) ? "TUGAS TAMBAHAN INDEX - ($tugasNote)" : null
                ];
                $value["totalSkor"] = $value['totalSkor'] + $tugasSkor;
                $value["dataSkor"]  = array_merge($value["dataSkor"],$data);
            }else if ($type == 3) {
                //get performa index
                $performaIndex = Performa_index::from("performa_index as pi")
                ->where("pi.emp_id", $value['id'])
                    ->whereRaw("('" . date('Y-m-d') . "' <= pi.expired_date)")
                    ->join("detail_indikator as di", "di.detail_id", "=", "pi.perform_skor")
                    ->join("indikator as i", "i.id", "=", "di.indikator_id");
                $performSkor = 0;
                $performanNote = [];
                if ($performaIndex->count() > 0) {
                    foreach ($performaIndex->get() as $x => $rs) {
                        $performSkor += ($rs->skor * $rs->bobot);
                        $performanNote[] = $rs->indikator;
                    }
                }
                $performanNote = implode(',', $performanNote);
                $data['performa'] = [
                    "skor"          => ($performSkor),
                    "keterangan"    => ($performSkor > 0) ? "($performanNote)" : null
                ];
                $value['skor_note']    = "Skor pegawai bulan ".$value["bulan_update"].".";
                $value["totalSkor"]    = $value['totalSkor'] + $performSkor;
                $value["dataSkor"]     = array_merge($value["dataSkor"],$data);
            }
            $newSkor[$key] = $value;
        }
        //update cache skor 
        Cache::put('skorPegawai',$newSkor);
        return response()->json([
            "code"      => 200,
            "message"   => "OK"
        ]);
    } */

    /* public function set_skor($type)
    {
        $skor = Cache::get('skorPegawai');

        // Memuat semua tugas tambahan, diklat, dan performa index dengan eager loading
        $employeeIds = collect($skor)->pluck('id')->toArray();
        $tugasTambahan = Tugas_tambahan::whereIn('emp_id', $employeeIds)
            ->whereDate('tanggal_akhir', '>=', now())
            ->with(['detailIndikator.indikator'])
            ->get();

        $performaIndex = Performa_index::whereIn('emp_id', $employeeIds)
            ->whereDate('expired_date', '>=', now())
            ->with(['detailIndikator.indikator'])
            ->get();

        $diklat = Diklat::whereIn('peserta_id', $employeeIds)
            ->with(['detailIndikator.indikator'])
            ->get();

        // Proses data skor untuk setiap pegawai
        $newSkor = [];
        foreach ($skor as $key => $value) {
            if ($type == 1) {
                // Proses sertifikat skor
                $sertifikatSkor = 0;
                $sertifikatNote = [];

                foreach ($diklat as $d) {
                    if ($d->peserta_id == $value['id']) {
                        $sertifikatSkor += ($d->skor * $d->detailIndikator->bobot);
                        $sertifikatNote[] = $d->judul_pelatihan;
                    }
                }

                if ($sertifikatSkor > 0.8) {
                    $sertifikatSkor = 0.8;
                }

                $sertifikatNote = implode(',', $sertifikatNote);

                $data['certifycate'] = [
                    "skor" => $sertifikatSkor,
                    "keterangan" => ($sertifikatSkor > 0) ? "DIKLAT & SERTIFIKAT INDEX - ($sertifikatNote)" : NULL
                ];

                $value["totalSkor"] += $sertifikatSkor;
                $value["dataSkor"] = array_merge($value["dataSkor"], $data);
            } elseif ($type == 2) {
                // Proses tugas tambahan skor
                $tugasSkor = 0;
                $tugasNote = [];

                foreach ($tugasTambahan as $tugas) {
                    if ($tugas->emp_id == $value['id']) {
                        $tugasSkor += ($tugas->skor * $tugas->detailIndikator->bobot);
                        $tugasNote[] = $tugas->detailIndikator->detail_name . '@' . $tugas->nama_tugas;
                    }
                }

                if ($value["is_medis"] == 'f' && $tugasSkor > 3.5) {
                    $tugasSkor = 3.5;
                }

                $tugasNote = implode(',', $tugasNote);

                $data['tugas'] = [
                    "skor" => $tugasSkor,
                    "keterangan" => ($tugasSkor > 0) ? "TUGAS TAMBAHAN INDEX - ($tugasNote)" : null
                ];

                $value["totalSkor"] += $tugasSkor;
                $value["dataSkor"] = array_merge($value["dataSkor"], $data);
            } elseif ($type == 3) {
                // Proses performa index skor
                $performSkor = 0;
                $performanNote = [];

                foreach ($performaIndex as $performa) {
                    if ($performa->emp_id == $value['id']) {
                        $performSkor += ($performa->skor * $performa->detailIndikator->bobot);
                        $performanNote[] = $performa->detailIndikator->indikator;
                    }
                }

                $performanNote = implode(',', $performanNote);

                $data['performa'] = [
                    "skor" => $performSkor,
                    "keterangan" => ($performSkor > 0) ? "($performanNote)" : null
                ];

                $value['skor_note'] = "Skor pegawai bulan " . $value["bulan_update"] . ".";
                $value["totalSkor"] += $performSkor;
                $value["dataSkor"] = array_merge($value["dataSkor"], $data);
            }

            $newSkor[$key] = $value;
        }
        dd($newSkor[100]);
        // Update cache skor
        Cache::put('skorPegawai', $newSkor, 3000);

        return response()->json([
            "code" => 200,
            "message" => "OK"
        ]);
    } */


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
        $request['skor_type']   = 1;
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
