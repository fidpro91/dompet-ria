<?php

namespace App\Http\Controllers;

use App\Models\Kategori_potongan;
use App\Models\Klasifikasi_pajak_penghasilan;
use App\Models\Pencairan_jasa;
use Illuminate\Http\Request;
use App\Models\Potongan_jasa_medis;
use App\Models\Potongan_penghasilan;
use App\Models\Potongan_statis;
use Illuminate\Support\Facades\Validator;
use DataTables;
use Exception;
use fidpro\builder\Create;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Potongan_jasa_medisController extends Controller
{
    public $model   = "Potongan_jasa_medis";
    public $folder  = "potongan_jasa_medis";
    public $route   = "potongan_jasa_medis";

    public $param = [
        'pencairan_id'   =>  '',
        'potongan_nama'   =>  '',
        'jasa_brutto'   =>  '',
        'penghasilan_pajak'   =>  '',
        'percentase_pajak'   =>  '',
        'potongan_value'   =>  'required',
        'medis_id_awal'   =>  '',
        'akumulasi_penghasilan_pajak'   =>  '',
        'master_potongan'   =>  '',
        'kategori_id'   =>  '',
        'header_id'   =>  ''
    ];
    public $defaultValue = [
        'potongan_id'   =>  '',
        'pencairan_id'   =>  '',
        'potongan_nama'   =>  '',
        'jasa_brutto'   =>  '',
        'penghasilan_pajak'   =>  '',
        'percentase_pajak'   =>  '',
        'potongan_value'   =>  '',
        'medis_id_awal'   =>  '',
        'akumulasi_penghasilan_pajak'   =>  '',
        'master_potongan'   =>  '',
        'kategori_id'   =>  '',
        'header_id'   =>  ''
    ];
    public function index()
    {
        return $this->themes($this->folder . '.index', null, $this);
    }

    public function get_dataTable(Request $request)
    {
        $data = Potongan_jasa_medis::select(
            [
                'potongan_id',
                'pencairan_id',
                'potongan_nama',
                'jasa_brutto',
                'penghasilan_pajak',
                'percentase_pajak',
                'potongan_value',
                'medis_id_awal',
                'akumulasi_penghasilan_pajak',
                'master_potongan',
                'kategori_id',
                'header_id'
            ]
        );

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>", [
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route . ".edit", $data->potongan_id),
                "ajax-url"  => route($this->route . '.update', $data->potongan_id),
                "data-target"  => "page_potongan_jasa_medi"
            ]);

            $button .= Create::action("<i class=\"fas fa-trash\"></i>", [
                "class"     => "btn btn-danger btn-xs",
                "onclick"   => "delete_row(this)",
                "x-token"   => csrf_token(),
                "data-url"  => route($this->route . ".destroy", $data->potongan_id),
            ]);
            return $button;
        })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function create(Request $request)
    {
        $cachePencairan = Cache::get("data_pencairan_".Auth()->id());
        $pegawai = Pencairan_jasa::from("pencairan_jasa as pj")
        ->join("employee as e","e.emp_id","=","pj.emp_id")
        ->where("id_header",$cachePencairan->id_cair_header)
        ->pluck("e.emp_name", "pj.id_cair")
        ->toArray();

        $listPencairan=[];
        foreach ($pegawai as $key => $value) {
            $listPencairan[] = [
                $key    => $value
            ];
        }
        $data = [
            "potongan_jasa_medis"   => (object)$this->defaultValue,
            "pegawai"               => $listPencairan
        ];
        return view($this->folder . '.form', $data);
    }

    public function hitung_potongan_statis($req,$kategoriPotongan,$detailPencairan,$pencairan)
    {
        DB::beginTransaction();
        try {
            $potonganPenghasilan = Potongan_penghasilan::where([
                "id_cair_header"    => $pencairan->id_cair_header,
                "kategori_potongan" => $req->kategori_potongan
            ])->first();
            if (!$potonganPenghasilan) {
                throw new Exception("Data perhitungan dasar belum ada",201);
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
                    "header_id"             => $potonganPenghasilan->id
                ];
                Potongan_jasa_medis::create($insertPotongan);
                DB::table("pencairan_jasa")->where("id_cair",$data->id_cair)
                ->update([
                    "total_potongan"    => $data->total_potongan+$pajak,
                    "total_netto"       => $data->total_brutto-($data->total_potongan+$pajak)
                ]);
                $totalPotongan += $pajak;
            }
            $potongan=Potongan_penghasilan::find($potonganPenghasilan->id);
            $potongan->update([
                "total_potongan"    => $potongan->total_potongan+$totalPotongan
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

    public function store(Request $request)
    {
        /* $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $valid['message']
            ]);
        } */
        $cachePencairan = Cache::get("data_pencairan_".Auth()->id());
        $detailPencairan = Pencairan_jasa::from("pencairan_jasa as pj")
        ->join("employee as e","e.emp_id","=","pj.emp_id")
        ->where("id_cair",$request->pencairan_id)
        ->get();

        $kategoriPotongan = Kategori_potongan::findOrFail($request->kategori_potongan);
        switch ($request->jenis_potongan) {
            case '244':
                $resp = $this->hitung_potongan_statis($request,$kategoriPotongan,$detailPencairan,$cachePencairan);
                break;
            
            case '245':
                $resp = $this->pajak_blud_medis($request,$kategoriPotongan,$detailPencairan,$cachePencairan);
                break;
            
            case '246':
                $resp = $this->pajak_blud_nonmedis($request,$kategoriPotongan,$detailPencairan,$cachePencairan);
                break;
            
            default:
                # code...
                break;
        }
        /* try {
            Potongan_jasa_medis::create($valid['data']);
            $resp = [
                'success' => true,
                'message' => 'Data Berhasil Disimpan!'
            ];
        } catch (\Exception $e) {
            $resp = [
                'success' => false,
                'message' => 'Data Gagal Disimpan! <br>' . $e->getMessage()
            ];
        } */
        return response()->json($resp);
    }

    public function pajak_blud_medis($req,$kategoriPotongan,$detailPencairan,$pencairan)
    {
        DB::beginTransaction();
        try {
            $potonganPenghasilan = Potongan_penghasilan::where([
                "id_cair_header"    => $pencairan->id_cair_header,
                "kategori_potongan" => $req->kategori_potongan
            ])->first();
            if (!$potonganPenghasilan) {
                throw new Exception("Data perhitungan dasar belum ada",201);
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
                /* $totalJasaBrutto = DB::select("
                    select max(akumulasi_penghasilan_pajak)pajak,max(pm.percentase_pajak)pajak_old from potongan_jasa_medis pm
                    join pencairan_jasa pj on pm.pencairan_id = pj.id_cair
                    join employee e on e.emp_id = pj.emp_id
                    where e.emp_id = '".$value->emp_id."' and pm.potongan_nama = 'PAJAK BLUD MEDIS'
                    AND DATE_FORMAT(tanggal_cair, '%Y') = '".date('Y')."';
                "); */
                $totalJasaBrutto = DB::select("
                    select (brutto)pajak,(pajak)pajak_old from pajak_dokter_blud
                    where emp_id = '".$value->emp_id."';
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
                        "potongan_nama"		            => $kategoriPotongan->nama_kategori,
                        "jasa_brutto"		            => $value->total_brutto,
                        "pencairan_id"                  => $value->id_cair,
                        "penghasilan_pajak"	            => $pj1,
                        "percentase_pajak"	            => $totalJasaBrutto->pajak_old,
                        "potongan_value"				=> $pajak_lama,
                        "akumulasi_penghasilan_pajak"	=> $limitPajak,
                        "kategori_id"                   => 4,
                        "header_id"                     => $potonganPenghasilan->id
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
                        "header_id"             => $potonganPenghasilan->id
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
                        "header_id"             => $potonganPenghasilan->id
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
            $potongan=Potongan_penghasilan::find($potonganPenghasilan->id);
            $potongan->update([
                "total_potongan"    => $potongan->total_potongan+$totalPotongan
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

    public function pajak_blud_nonmedis($req,$kategoriPotongan,$detailPencairan,$pencairan) {
        DB::beginTransaction();
            try {
                $potonganPenghasilan = Potongan_penghasilan::where([
                    "id_cair_header"    => $pencairan->id_cair_header,
                    "kategori_potongan" => $req->kategori_potongan
                ])->first();
                if (!$potonganPenghasilan) {
                    throw new Exception("Data perhitungan dasar belum ada",201);
                }
                $totalPotongan=0;
                foreach ($detailPencairan as $key => $value) {
                    if ($value->is_medis == "t" || empty($value->kode_ptkp)) {
                        continue;
                    }
                    if ($value->emp_status == 1) {
                        continue;
                    }
                    $cekPajak = Potongan_statis::where("pot_stat_code",$value->kode_ptkp)->first();
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
                    }
                    $pajakBlud = [
                        "potongan_nama"		=> $kategoriPotongan->nama_kategori,
                        "jasa_brutto"		=> $value->total_brutto,
                        "penghasilan_pajak"	=> $penghasilanWajibPajak,
                        "percentase_pajak"	=> $pajakPercent,
                        "potongan_value"	=> $pajak,
                        "akumulasi_penghasilan_pajak"	=> 0,
                        "pencairan_id"      => $value->id_cair,
                        "header_id"         => $potonganPenghasilan->id
                    ];
                    
                    Potongan_jasa_medis::create($pajakBlud);
                    DB::table("pencairan_jasa")->where("id_cair",$value->id_cair)
                    ->update([
                        "total_potongan"    => $value->total_potongan+$pajak,
                        "total_netto"       => $value->total_brutto-($value->total_potongan+$pajak)
                    ]);
                    $totalPotongan += $pajak;
                }
                $potongan=Potongan_penghasilan::find($potonganPenghasilan->id);
                $potongan->update([
                    "total_potongan"    => $potongan->total_potongan+$totalPotongan
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

    public function edit(Potongan_jasa_medis $potongan_jasa_medis)
    {
        return view($this->folder . '.form', compact('potongan_jasa_medis'));
    }

    public function update(Request $request, Potongan_jasa_medis $potongan_jasa_medis)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = Potongan_jasa_medis::findOrFail($potongan_jasa_medis->potongan_id);
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
        $data = Potongan_jasa_medis::findOrFail($id);
        Pencairan_jasa::find($data->pencairan_id)->update([
            "total_potongan"    => DB::raw("total_potongan - ".$data->potongan_value),
            "total_netto"       => DB::raw("total_potongan + ".$data->potongan_value)
        ]);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }
}
