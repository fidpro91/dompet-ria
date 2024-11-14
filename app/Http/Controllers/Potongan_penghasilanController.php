<?php

namespace App\Http\Controllers;

use App\Models\Kategori_potongan;
use App\Models\Klasifikasi_pajak_penghasilan;
use App\Models\Pencairan_jasa;
use App\Models\Pencairan_jasa_header;
use App\Models\Potongan_jasa_individu;
use App\Models\Potongan_jasa_medis;
use Illuminate\Http\Request;
use App\Models\Potongan_penghasilan;
use App\Models\Potongan_statis;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Potongan_penghasilanController extends Controller
{
    public $model   = "Potongan_penghasilan";
    public $folder  = "potongan_penghasilan";
    public $route   = "potongan_penghasilan";

    public $param = [
        'pajak_no'   =>  'required',
        'id_cair_header'   =>  'required',
        'kategori_potongan'   =>  'required',
        'potongan_method'   =>  'required',
        'created_by'   =>  'required',
    ];
    
    public $defaultValue = [
        'id'   =>  '',
        'pajak_no'   =>  '',
        'id_cair_header'   =>  '',
        'kategori_potongan'   =>  '',
        'total_potongan'   =>  '',
        'potongan_method'   =>  '1',
        'created_by'   =>  '',
    ];

    public function index($id_cair)
    {
        $data["pencairan"] = Pencairan_jasa_header::find($id_cair);
        Cache::put("data_pencairan_".Auth()->id(),$data["pencairan"]);

        return $this->themes($this->folder . '.group_potongan',$data, $this);
    }

    public function data($kategori_potongan)
    {
        return view($this->folder . '.data', compact('kategori_potongan'));
    }

    public function get_dataTable(Request $request)
    {
        $data = Potongan_penghasilan::from("potongan_penghasilan as pp")
                ->where([
                    "pp.kategori_potongan"  => $request->kategori_potongan,
                    "pp.id_cair_header"     => $request->id_cair,
                ])
                ->join("potongan_jasa_medis as pm","pm.header_id","=","pp.id")
                ->join("pencairan_jasa as pj",function($join){
                    $join->on('pj.id_cair', '=', 'pm.pencairan_id');
                })
                ->join("employee as e","e.emp_id","=","pj.emp_id")
                ->join("ms_unit as mu", "mu.unit_id", "=", "e.unit_id_kerja")
                ->select(
                    [
                        'pp.*',
                        "e.nomor_rekening",
                        "e.emp_name",
                        "mu.unit_name as unit_kerja", 
                        "e.golongan", 
                        "e.kode_ptkp", 
                        "e.gaji_pokok", 
                        "pm.jasa_brutto",
                        DB::raw("coalesce(pm.akumulasi_penghasilan_pajak,(e.gaji_pokok+pm.jasa_brutto)) as akumulasi_pendapatan"), 
                        "pm.penghasilan_pajak", 
                        "pm.percentase_pajak", 
                        "pm.potongan_value",
                        "pm.potongan_id"
                    ]
                );
        
        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            /* $button = Create::action("<i class=\"fas fa-edit\"></i>", [
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route . ".edit", $data->id),
                "ajax-url"  => route($this->route . '.update', $data->id),
                "data-target"  => "page_potongan_penghasilan"
            ]); */

            $button = Create::action("<i class=\"fas fa-trash\"></i>", [
                "class"     => "btn btn-danger btn-xs",
                "onclick"   => "delete_row(this)",
                "x-token"   => csrf_token(),
                "data-url"  => route("potongan_jasa_medis.destroy", $data->potongan_id),
            ]);

            /* $button .= Create::action("<i class=\"fas fa-glasses\"></i>", [
                "class"     => "btn btn-success btn-xs",
                "onclick"   => "show_potongan(".$data->id.")",
            ]); */

            return $button;
        })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function create()
    {
        $potongan_penghasilan = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('potongan_penghasilan'));
    }

    public function store(Request $request)
    {
        //get data pencairan jasa
        $pencairanJasa = Pencairan_jasa_header::find($request->id_cair_header);
        if (!$pencairanJasa) {
            $resp = [
                "success"   => false,
                "message"   => "Data pencairan tidak ditemukan"
            ];
            return response()->json($resp);
        }

        if ($pencairanJasa->is_published == 1) {
            $resp = [
                "success"   => false,
                "message"   => "Data pencairan sudah dicairkan"
            ];
            return response()->json($resp);
        }

        //cek apakah data sudah pernah dihitung
        $saved = Potongan_penghasilan::where([
            "id_cair_header"    => $request->id_cair_header,
            "kategori_potongan" => $request->kategori_potongan
        ])->first();

        if ($saved) {
            $resp = [
                "success"   => false,
                "message"   => "Data pencairan sudah dicairkan"
            ];
            return response()->json($resp);
        }

        // get data detail pencairan jasa
        $detailPencairan = Cache::remember('pencairan_jasa','60', function () use ($request) {
            return Pencairan_jasa::from("pencairan_jasa as pj")
                            ->join("employee as e","e.emp_id","=","pj.emp_id")
                            ->where("id_header",$request->id_cair_header)
                            ->get();
        });
        //get data kategori potongan
        $resp = $this->get_detail_potongan($request,$detailPencairan);

        /* foreach ($detailPencairan as $key => $value) {
            $fillable = [
                'pencairan_id'          => $value->id_cair,
                'potongan_nama'         => "",
                'jasa_brutto'           => $value->total_brutto,
                'penghasilan_pajak'     => "",
                'percentase_pajak'      => "",
                'potongan_value'        => "",
                'akumulasi_penghasilan_pajak'   => "",
                'master_potongan'               => "",
                'kategori_id'                   => ""
            ];
        } */

        return response()->json($resp);
    }

    public function show($id)
    {
        $potongan = Potongan_penghasilan::from("potongan_penghasilan as pp")
        ->join("kategori_potongan as kp", "kp.kategori_potongan_id", "=", "pp.kategori_potongan")
        ->where("id", $id)
        ->first();

        $data = Potongan_jasa_medis::from("potongan_jasa_medis as pm")
            ->join("pencairan_jasa as pj", "pj.id_cair", "=", "pm.pencairan_id")
            ->join("employee as e", "e.emp_id", "=", "pj.emp_id")
            ->join("ms_unit as mu", "mu.unit_id", "=", "e.unit_id_kerja")
            ->where("pm.header_id", $id);

        if ($potongan->is_pajak == "t") {
            $data->select(["e.nomor_rekening", "e.emp_name","mu.unit_name as unit_kerja", "e.golongan", "e.kode_ptkp", "e.gaji_pokok", "pm.jasa_brutto",
            DB::raw("coalesce(pm.akumulasi_penghasilan_pajak,(e.gaji_pokok+pm.jasa_brutto)) as akumulasi_pendapatan")
            , "pm.penghasilan_pajak", "pm.percentase_pajak", "pm.potongan_value"]);
        } else {
            $data->select(["e.nomor_rekening", "e.emp_name","mu.unit_name as unit_kerja", "pm.jasa_brutto", "pm.potongan_value"]);
        }
        // Tambahkan orderBy jika diperlukan
        $data->orderBy("ordering_mode", "asc")
             ->orderBy("mu.unit_name", "asc")
             ->orderBy("e.emp_name", "asc");
        // Simpan hasil query ke dalam variabel
        $data = $data->get();

        $table = \fidpro\builder\Bootstrap::tableData($data, ["class" => "table table-bordered"]);
        $titlePage = $potongan->nama_kategori;
        return view("potongan_penghasilan/view_potongan", compact('potongan', 'table','titlePage'));
    }

    
    public function get_detail_potongan($req,$detailPencairan)
    {
        $kategoriPotongan = Kategori_potongan::find($req->kategori_potongan);

        if ($kategoriPotongan->is_pajak == 't') {
            $resp = $this->hitung_pajak($req,$kategoriPotongan,$detailPencairan);
        }else {
            if ($kategoriPotongan->potongan_type == 2) {
                //potongan data individu
                $potonganIndividu = Potongan_jasa_individu::from("potongan_jasa_individu as pi")
                                    ->join("employee as e","e.emp_id","=","pi.emp_id")
                                    ->join("pencairan_jasa as pj","e.emp_id","=","pj.emp_id")
                                    ->where([
                                        "id_header"             => $req->id_cair_header,
                                        "pi.pot_status"         => "t",
                                        "kategori_potongan"     => $req->kategori_potongan
                                    ])->get();
                DB::beginTransaction();
                try {
                    $header = $this->insert_header($req);
                    if ($header["success"] == false) {
                        DB::rollBack();
                        return $header;
                    }
                    $totalPotongan=0;
                    foreach ($potonganIndividu as $key => $value) {
                        $dataPotongan = [
                            'pencairan_id'          => $value->id_cair,
                            'potongan_nama'         => $kategoriPotongan->nama_kategori,
                            'jasa_brutto'           => $value->total_brutto,
                            'potongan_value'        => $value->potongan_value,
                            'header_id'             => $header["header_id"]
                        ];
                        Potongan_jasa_medis::create($dataPotongan);
                        DB::table("pencairan_jasa")->where("id_cair",$value->id_cair)
                        ->update([
                            "total_potongan"    => $value->total_potongan+$value->potongan_value,
                            "total_netto"       => $value->total_brutto-($value->total_potongan+$value->potongan_value)
                        ]);
                        $lastAngsuran = $value->last_angsuran + 1;
                        $aktif = "t";
                        
                        if (($lastAngsuran == $value->max_angsuran)) {
                            $aktif = "f";
                        }
                        
                        Potongan_jasa_individu::find($value->pot_ind_id)->update([
                            "pot_status"    => $aktif,
                            "last_angsuran" => $lastAngsuran
                        ]);
                        $totalPotongan += $value->potongan_value;
                    }
                    Potongan_penghasilan::find($header["header_id"])->update([
                        "total_potongan"    => $totalPotongan
                    ]);
                    DB::commit();
                    $resp = [
                        'success'       => true,
                        'message'       => 'Data Berhasil Disimpan!'
                    ];
                } catch (\Exception $e) {
                    DB::rollBack();
                    $resp = [
                        'success' => false,
                        'message' => 'Data Gagal Disimpan! <br>' . $e->getMessage()
                    ];
                }
            }else {
                $resp = $this->hitung_potongan_statis($req,$kategoriPotongan,$detailPencairan);
            }
        }
        return $resp;
    }

    public function hitung_potongan_statis($req,$kategoriPotongan,$detailPencairan)
    {
        DB::beginTransaction();
        try {
            $potonganPenghasilan = $this->insert_header($req);
            if ($potonganPenghasilan["success"] == false) {
                DB::rollBack();
                return $potonganPenghasilan;
            }
            $totalPotongan=0;
            foreach ($detailPencairan as $key => $data) {
                //get data potongan statis
                $query = DB::selectOne("SELECT ps.* FROM potongan_statis ps
                WHERE '".$data->kode_golongan."' = pot_stat_code and kategori_potongan = $req->kategori_potongan");

                //cek apakah ada data potongan
                if (!$query) {
                    continue;
                }

                //CEK AGAMA FOR INFAQ
                if ($kategoriPotongan->nama_kategori == 'INFAQ') {
                    if (strtolower($data->agama) != 'islam') {
                        continue;
                    }
                }
                $jasa = $data->total_brutto;
                if ($query->potongan_type == 1) {
                    $percent = $query->potongan_nominal;
                    $pajak = $jasa*$query->potongan_nominal/100;
                }else{
                    $percent = 0;
                    $pajak = $query->potongan_nominal;
                }
                $insertPotongan = [
                    'pencairan_id'          => $data->id_cair,
                    'potongan_nama'         => $kategoriPotongan->nama_kategori."-".$query->nama_potongan,
                    'jasa_brutto'           => $jasa,
                    'penghasilan_pajak'     => $jasa,
                    'percentase_pajak'      => $percent,
                    'potongan_value'        => $pajak,
                    "header_id"             => $potonganPenghasilan['header_id']
                ];
                Potongan_jasa_medis::create($insertPotongan);
                DB::table("pencairan_jasa")->where("id_cair",$data->id_cair)
                ->update([
                    "total_potongan"    => $data->total_potongan+$pajak,
                    "total_netto"       => $data->total_brutto-($data->total_potongan+$pajak)
                ]);
                $totalPotongan += $pajak;
            }
            Potongan_penghasilan::find($potonganPenghasilan["header_id"])->update([
                "total_potongan"    => $totalPotongan
            ]);
            DB::commit();
            $resp = [
                'success'       => true,
                'message'       => 'Data Berhasil Disimpan!'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            $resp = [
                'success' => false,
                'message' => 'Data Gagal Disimpan! <br>' . $e->getMessage()
            ];
        }

        return $resp;
    }

    public function hitung_pajak($req,$kategoriPotongan,$detailPencairan)
    {
        if ($kategoriPotongan->kategori_potongan_id == 1) {
            $resp = $this->hitung_potongan_statis($req,$kategoriPotongan,$detailPencairan);
        }elseif($kategoriPotongan->kategori_potongan_id == 3) {
            DB::beginTransaction();
            try {
                $potonganPenghasilan = $this->insert_header($req);
                if ($potonganPenghasilan["success"] == false) {
                    DB::rollBack();
                    return $potonganPenghasilan;
                }
                $totalPotongan=0;
                foreach ($detailPencairan as $key => $value) {
                    if ($value->is_medis == "t" || empty($value->kode_ptkp)) {
                        continue;
                    }
                    if ($value->emp_status == 1) {
                        continue;
                    }
                    /* $cekPajak = Potongan_statis::where("pot_stat_code",$value->kode_ptkp)->first();
                    if (!$cekPajak) {
                        continue;
                    }
                    $penghasilanWajibPajak = (($value->gaji_pokok)+$value->total_brutto)*12;
                    $penghasilanWajibPajak = $penghasilanWajibPajak-$cekPajak->potongan_nominal;
                    $pajakPercent=0;
                    $pajak=0;
                    if ($penghasilanWajibPajak > 0) {
                        $percent = Klasifikasi_pajak_penghasilan::whereRaw("'$penghasilanWajibPajak' >= batas_bawah AND '$penghasilanWajibPajak' < batas_atas")->first();
                        if (!$percent) {
                            return false;
                        }
                        $pajak = ($penghasilanWajibPajak*$percent->percentase_pajak/100)/12;
                        $pajakPercent = $percent->percentase_pajak;
                    } */
                    $penghasilanWajibPajak = (($value->gaji_pokok)+$value->total_brutto);
                    $cekPajak = DB::selectOne("
                        SELECT gp.percentase FROM ptkp_to_grade pg
                        JOIN grade_ptkp gp ON pg.grade_id = gp.id
                        JOIN potongan_statis ps ON ps.pot_stat_id = pg.ptkp_id
                        WHERE ps.pot_stat_code = '".$value->kode_ptkp."' AND (
                            $penghasilanWajibPajak BETWEEN batas_bawah AND batas_atas
                        )
                    ");
                    if (!$cekPajak) {
                        continue;
                    }
                    $pajak = $cekPajak->percentase*$penghasilanWajibPajak;
                    $pajakBlud = [
                        "potongan_nama"		=> $kategoriPotongan->nama_kategori,
                        "jasa_brutto"		=> $value->total_brutto,
                        "penghasilan_pajak"	=> $penghasilanWajibPajak,
                        "percentase_pajak"	=> $cekPajak->percentase,
                        "potongan_value"	=> $pajak,
                        "akumulasi_penghasilan_pajak"	=> 0,
                        "pencairan_id"      => $value->id_cair,
                        "header_id"         => $potonganPenghasilan["header_id"]
                    ];
                    
                    Potongan_jasa_medis::create($pajakBlud);
                    DB::table("pencairan_jasa")->where("id_cair",$value->id_cair)
                    ->update([
                        "total_potongan"    => $value->total_potongan+$pajak,
                        "total_netto"       => $value->total_brutto-($value->total_potongan+$pajak)
                    ]);
                    $totalPotongan += $pajak;
                }
                Potongan_penghasilan::find($potonganPenghasilan["header_id"])->update([
                    "total_potongan"    => $totalPotongan
                ]);
                DB::commit();
                $resp = [
                    'success'       => true,
                    'message'       => 'Data Berhasil Disimpan!'
                ];
            } catch (\Exception $e) {
                DB::rollBack();
                $resp = [
                    'success' => false,
                    'message' => 'Data Gagal Disimpan! <br>' . $e->getMessage()
                ];
            }
        }elseif($kategoriPotongan->kategori_potongan_id == 4) {
            $resp = $this->pajak_blud_medis($req,$kategoriPotongan,$detailPencairan);
        }else{
            $resp = [
                "success"   => false,
                "message"   => "Bukan kategori pajak"
            ];
        }

        return $resp;
    }

    /* public function pajak_blud_medis($req,$kategoriPotongan,$detailPencairan)
    {
        DB::beginTransaction();
        try {
            $potonganPenghasilan = $this->insert_header($req);
            if ($potonganPenghasilan["success"] == false) {
                DB::rollBack();
                return $potonganPenghasilan;
            }
            $totalPotongan=0;
            foreach ($detailPencairan as $key => $value) {
                $insertPotongan=[];
                if ($value->is_medis != "t") {
                    continue;
                }
                if ($value->emp_status == 1) {
                    continue;
                }
                $totalJasaBrutto = DB::select("
                    select max(akumulasi_penghasilan_pajak)pajak,max(pm.percentase_pajak)pajak_old from potongan_jasa_medis pm
                    join pencairan_jasa pj on pm.pencairan_id = pj.id_cair
                    join employee e on e.emp_id = pj.emp_id
                    where e.emp_id = '".$value->emp_id."' and pm.potongan_nama = 'PAJAK BLUD MEDIS'
                    AND DATE_FORMAT(tanggal_cair, '%Y') = '".date('Y')."';
                ");

                if (!empty($totalJasaBrutto)) {
                    $totalJasaBrutto=$totalJasaBrutto[0];
                }else{
                    $totalJasaBrutto = new \stdClass;
                    $totalJasaBrutto->pajak = 0;
                    $totalJasaBrutto->pajak_old = 0;
                }
                $penghasilanWajibPajak = $value->total_brutto*0.5;
                $limitPajak = $totalJasaBrutto->pajak+$penghasilanWajibPajak;
                $percent = Klasifikasi_pajak_penghasilan::whereRaw("'$limitPajak' >= batas_bawah AND '$limitPajak' < batas_atas")->first();

                if ($totalJasaBrutto->pajak_old != 0 && $totalJasaBrutto->pajak_old != $percent->percentase_pajak) {
                    $pj1 = $percent->batas_bawah - $totalJasaBrutto->pajak;
                    $pajak_lama = $pj1*$totalJasaBrutto->pajak_old/100;
                    $pj2 = $penghasilanWajibPajak-$pj1;
                    $pajak_baru = $pj2*$percent->percentase_pajak/100;
                    $insertPotongan[0] = [
                        "potongan_nama"		            => "PAJAK BLUD MEDIS",
                        "jasa_brutto"		            => $value->total_brutto,
                        "pencairan_id"                  => $value->id_cair,
                        "penghasilan_pajak"	            => $pj1,
                        "percentase_pajak"	            => $totalJasaBrutto->pajak_old,
                        "potongan_value"				=> $pajak_lama,
                        "akumulasi_penghasilan_pajak"	=> $limitPajak,
                        "kategori_id"                   => 4,
                        "header_id"                     => $potonganPenghasilan['header_id']
                    ];
                    $insertPotongan[1] = [
                        "potongan_nama"		=> "PAJAK BLUD MEDIS",
                        "jasa_brutto"		=> $value->total_brutto,
                        "pencairan_id"      => $value->id_cair,
                        "penghasilan_pajak"	=> $pj2,
                        "percentase_pajak"	=> $percent->percentase_pajak,
                        "potongan_value"	=> $pajak_baru,
                        "akumulasi_penghasilan_pajak"	=> $limitPajak,
                        "kategori_id"                   => 4,
                        "header_id"             => $potonganPenghasilan['header_id']
                    ];
                    $totalPotongan += ($pajak_lama+$pajak_baru);
                }else{
                    $pajak = $penghasilanWajibPajak*($percent->percentase_pajak/100);
                    $insertPotongan[] = [
                        "potongan_nama"		=> "PAJAK BLUD MEDIS",
                        "jasa_brutto"		=> $value->total_brutto,
                        "pencairan_id"      => $value->id_cair,
                        "penghasilan_pajak"	=> $penghasilanWajibPajak,
                        "percentase_pajak"	=> $percent->percentase_pajak,
                        "potongan_value"	=> $pajak,
                        "akumulasi_penghasilan_pajak"	=> $limitPajak,
                        "kategori_id"                   => 4,
                        "header_id"             => $potonganPenghasilan['header_id']
                    ];
                    $totalPotongan += $pajak;
                }
                Potongan_jasa_medis::insert($insertPotongan);
                DB::table("pencairan_jasa")->where("id_cair",$value->id_cair)
                ->update([
                    "total_potongan"    => $value->total_potongan+$pajak,
                    "total_netto"       => $value->total_brutto-($value->total_potongan+$pajak)
                ]);
                $totalPotongan += $pajak;
            }
            Potongan_penghasilan::find($potonganPenghasilan["header_id"])->update([
                "total_potongan"    => $totalPotongan
            ]);
            DB::commit();
            $resp = [
                'success'       => true,
                'message'       => 'Data Berhasil Disimpan!'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            $resp = [
                'success' => false,
                'message' => 'Data Gagal Disimpan! <br>' . $e->getMessage()
            ];
        }

        return $resp;
    } */

    public function pajak_blud_medis($req,$kategoriPotongan,$detailPencairan)
    {
        DB::beginTransaction();
        try {
            $potonganPenghasilan = $this->insert_header($req);
            if ($potonganPenghasilan["success"] == false) {
                DB::rollBack();
                return $potonganPenghasilan;
            }
            $totalPotongan=0;
            foreach ($detailPencairan as $key => $value) {
                $insertPotongan=[];
                if ($value->is_medis != "t") {
                    continue;
                }
                if ($value->emp_status == 1) {
                    continue;
                }

                /* 
                    Rumus potongan baru
                    brutox50%x5%
                */
                $penghasilanWajibPajak = ($value->total_brutto*0.5);
                $pajak = $penghasilanWajibPajak*0.05;
                $insertPotongan = [
                    "potongan_nama"		            => "PAJAK BLUD MEDIS",
                    "jasa_brutto"		            => $value->total_brutto,
                    "pencairan_id"                  => $value->id_cair,
                    "penghasilan_pajak"	            => $penghasilanWajibPajak,
                    "percentase_pajak"	            => 5,
                    "potongan_value"	            => $pajak,
                    // "akumulasi_penghasilan_pajak"	=> $limitPajak,
                    "kategori_id"                   => 4,
                    "header_id"                     => $potonganPenghasilan['header_id']
                ];

                $totalPotongan += $pajak;

                Potongan_jasa_medis::insert($insertPotongan);
                DB::table("pencairan_jasa")->where("id_cair",$value->id_cair)
                ->update([
                    "total_potongan"    => $value->total_potongan+$pajak,
                    "total_netto"       => $value->total_brutto-($value->total_potongan+$pajak)
                ]);
                $totalPotongan += $pajak;
            }
            Potongan_penghasilan::find($potonganPenghasilan["header_id"])->update([
                "total_potongan"    => $totalPotongan
            ]);
            DB::commit();
            $resp = [
                'success'       => true,
                'message'       => 'Data Berhasil Disimpan!'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            $resp = [
                'success' => false,
                'message' => 'Data Gagal Disimpan! <br>' . $e->getMessage()
            ];
        }

        return $resp;
    }

    public function insert_header($req)
    {
        $req["created_by"]  = Auth::id();
        $req["pajak_no"]    = "1234";
        $valid = $this->form_validasi($req->all());
        if ($valid['code'] != 200) {
            return [
                'success' => false,
                'message' => $valid['message']
            ];
        }
        try {
            $insert = Potongan_penghasilan::create($valid['data']);
            $resp = [
                'success'       => true,
                'message'       => 'Data Berhasil Disimpan!',
                "header_id"     => $insert->id 
            ];
        } catch (\Exception $e) {
            $resp = [
                'success' => false,
                'message' => 'Data Gagal Disimpan! <br>' . $e->getMessage()
            ];
        }
        return ($resp);
    }

    public function commited(Request $request)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $valid['message']
            ]);
        }
        try {
            Potongan_penghasilan::create($valid['data']);
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

    public function edit(Potongan_penghasilan $potongan_penghasilan)
    {
        return view($this->folder . '.form', compact('potongan_penghasilan'));
    }
    public function update(Request $request, Potongan_penghasilan $potongan_penghasilan)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = Potongan_penghasilan::findOrFail($potongan_penghasilan->id);
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

    public function destroy_all(Request $request)
    {
        $data = Potongan_penghasilan::where([
            "kategori_potongan" => $request->kategori_potongan,
            "id_cair_header"    => $request->id_cair
        ])->first();
        //cek status pencairan
        $pencairan = Pencairan_jasa_header::find($data->id_cair_header);
        if ($pencairan->is_published == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Data pencairan sudah selesai. Data tidak dapat dihapus.'
            ]);
        }
        DB::beginTransaction();
        try {
            DB::statement("UPDATE potongan_jasa_individu pi
            JOIN (
                SELECT pi.pot_ind_id FROM potongan_jasa_individu pi
                join pencairan_jasa pj on pi.emp_id = pj.emp_id
                join potongan_jasa_medis pm ON pj.id_cair = pm.pencairan_id
                JOIN potongan_penghasilan ph ON pm.header_id = ph.id AND ph.kategori_potongan = pi.kategori_potongan
                WHERE pj.id_header = $request->id_cair and pi.kategori_potongan = $request->kategori_potongan
            ) x ON x.pot_ind_id = pi.pot_ind_id
            SET pi.last_angsuran = (pi.last_angsuran-1), pi.pot_status = 't'");
            Potongan_jasa_medis::where("header_id",$data->id)->delete();
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

    public function destroy($id)
    {
        $data = Potongan_penghasilan::findOrFail($id);
        
        //cek status pencairan
        $pencairan = Pencairan_jasa_header::find($data->id_cair_header);
        if ($pencairan->is_published == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Data pencairan sudah selesai. Data tidak dapat dihapus.'
            ]);
        }

        Potongan_jasa_medis::where("header_id",$id)->delete();
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }
}
