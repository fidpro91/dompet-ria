<?php

namespace App\Http\Controllers;

use App\Charts\RemunChart;
use App\Exports\PencairanExport;
use App\Libraries\Servant;
use App\Models\Employee;
use App\Models\Jasa_pelayanan;
use App\Models\Kategori_potongan;
use App\Models\Klasifikasi_pajak_penghasilan;
use App\Models\Pencairan_jasa;
use Illuminate\Http\Request;
use App\Models\Pencairan_jasa_header;
use App\Models\Potongan_jasa_individu;
use App\Models\Potongan_jasa_medis;
use App\Models\Potongan_penghasilan;
use App\Models\Potongan_statis;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use stdClass;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class Pencairan_jasa_headerController extends Controller
{
    public $model   = "Pencairan_jasa_header";
    public $folder  = "pencairan_jasa_header";
    public $route   = "pencairan_jasa_header";
    public $totalPotongan = 0;

    public $param = [
        'no_pencairan'   =>  'required',
        'tanggal_cair'   =>  'required',
        'total_nominal'   =>  'required',
        'user_act'   =>  '',
        'created_at'   =>  '',
        'keterangan'   =>  '',
        'is_published'   =>  ''
    ];
    public $defaultValue = [
        'id_cair_header'   =>  '',
        'no_pencairan'   =>  '',
        'tanggal_cair'   =>  '',
        'total_nominal'   =>  '',
        'user_act'   =>  '',
        'created_at'   =>  'CURRENT_TIMESTAMP',
        'keterangan'   =>  '',
        'is_published'   =>  '0'
    ];
    public function index()
    {
        
        return $this->themes($this->folder . '.index',null, $this);
    }

    public function get_dataTable(Request $request)
    {
        $data = Pencairan_jasa_header::select([
            'id_cair_header',
            'no_pencairan',
            'tanggal_cair',
            'total_nominal',
            'user_act',
            'created_at',
            'keterangan',
            'is_published'
        ]);
        $data->orderBy("tanggal_cair","desc");

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::link("<i class=\"far fa-file-pdf\"></i>", [
                "class"     => "btn btn-info btn-xs",
                "href"      => url("$this->route/print/$data->id_cair_header"),
                "target"    => "_blank"
            ]);
            $button .= Create::link("<i class=\"far fa-file-excel\"></i>", [
                "class"     => "btn btn-success btn-xs",
                "href"      => url("$this->route/excel/$data->id_cair_header"),
            ]);

            $button .= Create::link("<i class=\" fas fa-chart-bar\"></i>", [
                "class"     => "btn btn-purple btn-xs",
                "href"      => url("$this->route/statistik/$data->id_cair_header"),
            ]);

            if ($data->is_published == 0) {
                $button .= Create::link("<i class=\"fas fa-glasses\"></i>", [
                    "class"     => "btn btn-warning btn-xs",
                    "href"      => url("potongan_penghasilan/index/$data->id_cair_header"),
                    // "href"      => url("$this->route/kroscek/$data->id_cair_header"),
                    "target"    => "_blank"
                ]);
                $button .= Create::action("<i class=\"fas fa-trash\"></i>", [
                    "class"     => "btn btn-danger btn-xs",
                    "onclick"   => "delete_row(this)",
                    "x-token"   => csrf_token(),
                    "data-url"  => route($this->route . ".destroy", $data->id_cair_header),
                ]);
            }
            return $button;
        })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function create()
    {
        $pencairan_jasa_header = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('pencairan_jasa_header'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $totalJasa = Jasa_pelayanan::whereIn("jaspel_id",$request->jaspel_id)->sum("nominal_jaspel");
            
            $input = [
                'no_pencairan'   =>  $request->no_pencairan,
                'tanggal_cair'   =>  date("Y-m-d",strtotime($request->tanggal_cair)),
                'total_nominal'  =>  $totalJasa,
                'user_act'       =>  Auth::user()->id,
                'keterangan'     =>  $request->keterangan
            ];
            $save=Pencairan_jasa_header::create($input);
            $idHeader = $save->id_cair_header;
            Jasa_pelayanan::whereIn("jaspel_id",$request->jaspel_id)->update([
                "id_cair"   => $idHeader,
                "status"    => 3
            ]);
            $byName = DB::table("jp_byname_medis as jm")
                      ->join("employee as e","e.emp_id","=","jm.emp_id")
                      ->whereIn("jaspel_id",$request->jaspel_id)
                      ->groupByRaw("e.agama,e.nomor_rekening,jm.kodepegawai,e.kode_ptkp,e.emp_name,e.kode_golongan,e.emp_id,e.is_medis,e.gaji_pokok,e.gaji_add,e.emp_status")
                      ->orderBy("e.emp_id")
                      ->selectRaw("
                        e.agama,e.nomor_rekening,e.emp_id,emp_status,kode_ptkp,is_medis,kode_golongan,kodepegawai,emp_name as nama_pegawai,sum(nominal_terima)total_terima,(e.gaji_pokok+e.gaji_add)gaji_pokok
                    ");
            foreach ($byName->get() as $key => $value) {
				$input = [
					"tanggal_cair" 		=> date('Y-m-d'),
					"emp_id"			=> $value->emp_id,
					"total_brutto"		=> $value->total_terima,
					"id_header"			=> $idHeader,
					"nomor_rekening"	=> $value->nomor_rekening
				];
                $id=DB::table("pencairan_jasa")->insertGetId($input);

                //hitung pajak & potongan by golongan
                /* $this->hitung_potongan_golongan($value,$id);
                if ($value->emp_status == 2) {
                    $this->hitung_pajak_blud($value,$id);
                }

                $this->hitung_potongan_individu($value,$id);
                DB::table("pencairan_jasa")->where("id_cair",$id)->update([
                    "total_potongan"	=> $this->totalPotongan,
					"total_netto"		=> ($value->total_terima-$this->totalPotongan)
                ]);
                $this->totalPotongan = 0; */
            }
            DB::commit();
            $resp = [
                'success' => true,
                'message' => 'Data Berhasil Disimpan!'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            $resp = [
                'success' => false,
                'message' => 'Data Gagal Disimpan! <br>' . $e->getMessage().$e->getLine()
            ];
        }
        return response()->json($resp);
    }

    private function hitung_potongan_golongan($data,$id)
    {
        $query = DB::select("SELECT ps.*,kp.nama_kategori FROM potongan_statis ps
        join kategori_potongan kp on ps.kategori_potongan = kp.kategori_potongan_id 
        WHERE '".$data->kode_golongan."' = pot_stat_code");
        $resp=[];
        foreach ($query as $key => $value) {
            //CEK AGAMA FOR INFAQ
            if ($value->nama_kategori == 'INFAQ') {
                if (strtolower($data->agama) != 'islam') {
                    continue;
                }
            }
            $jasa = $data->total_terima;
            if ($value->potongan_type == 1) {
                $percent = $value->potongan_nominal;
                $pajak = $jasa*$value->potongan_nominal/100;
            }else{
                $percent = 0;
                $pajak = $value->potongan_nominal;
            }
            $resp[] = [
                "pencairan_id"      => $id,
                "potongan_nama"		=> $value->nama_kategori."-".$value->nama_potongan,
                "jasa_brutto"		=> $jasa,
                "penghasilan_pajak"	=> $jasa,
                "percentase_pajak"	=> $percent,
                "potongan_value"	=> $pajak,
                "akumulasi_penghasilan_pajak"	=> 0,
                "master_potongan"   => $value->pot_stat_id,
                "kategori_id"       => $value->kategori_potongan
            ];
            $this->totalPotongan += $pajak;
        }
        DB::table("potongan_jasa_medis")->insert($resp);
    }

    private function hitung_pajak_blud($data,$id)
	{
		$resp=[];
        $jasa = $data->total_terima;
		if ($data->is_medis == 't') {
			$totalJasaBrutto = DB::select("
				select max(akumulasi_penghasilan_pajak)pajak,max(pm.percentase_pajak)pajak_old from potongan_jasa_medis pm
				join pencairan_jasa pj on pm.pencairan_id = pj.id_cair
				join employee e on e.emp_id = pj.emp_id
				where e.emp_id = '".$data->emp_id."' and pm.potongan_nama = 'PAJAK BLUD MEDIS'
				AND DATE_FORMAT(tanggal_cair, '%Y') = '".date('Y')."';
			");
            if (!empty($totalJasaBrutto)) {
                $totalJasaBrutto=$totalJasaBrutto[0];
            }else{
                $totalJasaBrutto = new stdClass;
                $totalJasaBrutto->pajak = 0;
				$totalJasaBrutto->pajak_old = 0;
            }
			$penghasilanWajibPajak = $jasa*0.5;
			$limitPajak = $totalJasaBrutto->pajak+$penghasilanWajibPajak;
			$percent = Klasifikasi_pajak_penghasilan::whereRaw("'$limitPajak' >= batas_bawah AND '$limitPajak' < batas_atas")->first();

			if ($totalJasaBrutto->pajak_old != 0 && $totalJasaBrutto->pajak_old != $percent->percentase_pajak) {
				$pj1 = $percent->batas_bawah - $totalJasaBrutto->pajak;
				$pajak_lama = $pj1*$totalJasaBrutto->pajak_old/100;
				$pj2 = $penghasilanWajibPajak-$pj1;
				$pajak_baru = $pj2*$percent->percentase_pajak/100;
				$resp[0] = [
					"potongan_nama"		            => "PAJAK BLUD MEDIS",
					"jasa_brutto"		            => $jasa,
                    "pencairan_id"                  => $id,
					"penghasilan_pajak"	            => $pj1,
					"percentase_pajak"	            => $totalJasaBrutto->pajak_old,
					"potongan_value"				=> $pajak_lama,
					"akumulasi_penghasilan_pajak"	=> $limitPajak,
                    "kategori_id"                   => 4
				];
				$resp[1] = [
					"potongan_nama"		=> "PAJAK BLUD MEDIS",
					"jasa_brutto"		=> $jasa,
                    "pencairan_id"      => $id,
					"penghasilan_pajak"	=> $pj2,
					"percentase_pajak"	=> $percent->percentase_pajak,
					"potongan_value"	=> $pajak_baru,
					"akumulasi_penghasilan_pajak"	=> $limitPajak,
                    "kategori_id"                   => 4
				];
                $this->totalPotongan += ($pajak_lama+$pajak_baru);
			}else{
				$pajak = $penghasilanWajibPajak*($percent->percentase_pajak/100);
				$resp[] = [
					"potongan_nama"		=> "PAJAK BLUD MEDIS",
					"jasa_brutto"		=> $jasa,
                    "pencairan_id"      => $id,
					"penghasilan_pajak"	=> $penghasilanWajibPajak,
					"percentase_pajak"	=> $percent->percentase_pajak,
					"potongan_value"	=> $pajak,
					"akumulasi_penghasilan_pajak"	=> $limitPajak,
                    "kategori_id"                   => 4
				];
                $this->totalPotongan += $pajak;
			}
		}else{
			if (!empty($data->kode_ptkp)) {
				$cekPajak = Potongan_statis::where("pot_stat_code",$data->kode_ptkp)->first();
                if (!$cekPajak) {
                    return false;
                }
				$penghasilanWajibPajak = ($data->gaji_pokok+$jasa)*12;
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
				$resp[] = [
					"potongan_nama"		=> "PAJAK BLUD NON MEDIS",
					"jasa_brutto"		=> $jasa,
					"penghasilan_pajak"	=> $penghasilanWajibPajak,
					"percentase_pajak"	=> $pajakPercent,
					"potongan_value"	=> $pajak,
					"akumulasi_penghasilan_pajak"	=> 0,
                    "pencairan_id"      => $id,
                    "master_potongan"   => $cekPajak->pot_stat_id,
                    "kategori_id"       => $cekPajak->kategori_potongan
				];
                $this->totalPotongan += $pajak;
			}
		}        
        DB::table("potongan_jasa_medis")->insert($resp);
	}

    public function hitung_potongan_individu($data,$id)
	{
		//hitung potongan individu
        $jasa = $data->total_terima;
		$potongan1 = Potongan_jasa_individu::from("potongan_jasa_individu as pj")
                     ->join("kategori_potongan as kp","kp.kategori_potongan_id","=","pj.kategori_potongan")
                     ->where([
                        "emp_id"			=> $data->emp_id,
						"pot_status"		=> "t"
                     ])->get();
		$resp1=[];
		foreach ($potongan1 as $key => $value) {
			if (($value->last_angsuran == 0) || ($value->last_angsuran != $value->max_angsuran)) {
				if ($value->potongan_type == 1) {
					$percent = $value->potongan_value;
					$pajak = $jasa*$value->potongan_value/100;
				}else{
					$percent = 0;
					$pajak = $value->potongan_value;
				}

				$angsuranKe = $value->last_angsuran+1;
				Potongan_jasa_individu::find($value->pot_ind_id)->update([
					"last_angsuran"	=> $angsuranKe
				]);

				$resp1[] = [
					"potongan_nama"		=> $value->nama_kategori,
					"jasa_brutto"		=> $jasa,
					"penghasilan_pajak"	=> $jasa,
					"percentase_pajak"	=> $percent,
					"potongan_value"	=> $pajak,
					"akumulasi_penghasilan_pajak"	=> 0,
                    "pencairan_id"      => $id,
                    "kategori_id"       => $value->kategori_potongan
				];
                
                $this->totalPotongan += $pajak;
			}
		}
        DB::table("potongan_jasa_medis")->insert($resp1);
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

    public function edit(Pencairan_jasa_header $pencairan_jasa_header)
    {
        return view($this->folder . '.form', compact('pencairan_jasa_header'));
    }
    public function update_potongan(Request $request)
    {
        try {
            DB::table('potongan_jasa_medis')->where("potongan_id",$request->potongan_id)
            ->update($request->all());
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
        $data = Pencairan_jasa_header::findOrFail($id);
        DB::beginTransaction();
        try {
            DB::statement("UPDATE potongan_jasa_individu pi
			JOIN (
                SELECT pi.pot_ind_id FROM potongan_jasa_individu pi
                join pencairan_jasa pj on pi.emp_id = pj.emp_id
                join potongan_jasa_medis pm ON pj.id_cair = pm.pencairan_id
                JOIN potongan_penghasilan ph ON pm.header_id = ph.id AND ph.kategori_potongan = pi.kategori_potongan
                WHERE pj.id_header = $id
			) x ON x.pot_ind_id = pi.pot_ind_id
			SET pi.last_angsuran = (pi.last_angsuran-1)");
            
            Jasa_pelayanan::where("id_cair",$id)->update([
                "status"    => 2,
                "id_cair"   => null
            ]);

            //potongan penghasilan
            $dataPotongan = Potongan_penghasilan::where("id_cair_header",$id)->get();
            foreach ($dataPotongan as $key => $value) {
                Potongan_jasa_medis::where("header_id",$value->id)->delete();
                Potongan_penghasilan::find($value->id)->delete();
            }

            Pencairan_jasa::where("id_header",$id)->delete();
            
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

    public function kroscek($id)
    {
        $data['header']     = Pencairan_jasa_header::find($id);
        $data['potongan']   = Kategori_potongan::where("potongan_active","t")->get()->toArray();

        return $this->themes($this->folder . '.printout.kroscek', compact('data'),"Kroscek Pencairan Jasa");
    }

    public function detail($type,$id_kategori,$id_jasa)
    {
       $potongan = DB::table("pencairan_jasa as pj")
                   ->join("potongan_jasa_medis as pm","pj.id_cair","=","pm.pencairan_id")
                   ->join("employee as e","e.emp_id","=","pj.emp_id")
                   ->where([
                    "pj.id_header"      => $id_jasa,
                    "pm.kategori_id"    => $id_kategori
                   ])->get();
        if ($type == 1) {
            return view($this->folder.'.printout.detail_potongan',compact('potongan'));
        }else{
            return view($this->folder.'.printout.detail_potongan2',compact('potongan'));
        }
    }

    public function final_pencairan($id)
	{
        DB::beginTransaction();
        try {
            $data=Pencairan_jasa_header::findOrfail($id);
            $data->update([
                "is_published" => 1
            ]);
            $ekseKutif = DB::select("SELECT 'EKSEKUTIF' AS penjamin,
            SUM(nominal_terima)total
            FROM jp_byname_medis jm
            JOIN jasa_pelayanan jp ON jm.jaspel_id = jp.jaspel_id
            WHERE jp.id_cair = $id AND jm.komponen_id = 9");
            $percent = $ekseKutif[0]->total/$data->total_nominal*100;
            $percentase[] = [
                "penjamin"          => "EKSEKUTIF",
                "persentase_jasa"   => ($percent),
                "id_cair"           => $id
            ];
            $medisNonEks = DB::select("
            SELECT mr.reff_name as penjamin,
            sum(dm.skor/10000) as total_skor
            FROM point_medis dm
            JOIN ms_reff mr on mr.reff_code = dm.penjamin
            JOIN jp_byname_medis jm ON jm.jp_medis_id = dm.jp_medis_id
            JOIN jasa_pelayanan jp ON jm.jaspel_id = jp.jaspel_id
            WHERE jp.id_cair = $id AND jm.komponen_id = 7
            GROUP BY mr.reff_name");
            $bagianNonEks=$data->total_nominal-$ekseKutif[0]->total;
            $medisArray = array_map(function ($medisNonEks) {
                return (array)$medisNonEks;
            }, $medisNonEks);
            $totalSkor = array_sum(array_column($medisArray,'total_skor'));
            foreach ($medisNonEks as $key => $value) {
                $hitungProporsi = ($value->total_skor/$totalSkor*$bagianNonEks)/$bagianNonEks*(100-$percent);
                $percentase[] = [
                    "penjamin"          => $value->penjamin,
                    "persentase_jasa"   => ($hitungProporsi),
                    "id_cair"           => $id
                ];
            }
            DB::table('persentase_jasa')->insert($percentase);

            //publish ke pegawai;
            $employee = Pencairan_jasa::from("pencairan_jasa as pj")
                        ->join("employee as e","e.emp_id","=","pj.emp_id")
                        ->where("pj.id_header",$id)
                        ->whereNotNull("e.phone")
                        ->get();
            $customKey = '@RSig2024';
            foreach ($employee as $key => $value) {
                $link = Crypt::encryptString($id."-".$value->emp_id,$customKey);
                $link = "http://localhost:88/slip_remun/download/".$link;

                $message = [
                    "message"   => "<b>$data->keterangan</b>.<br>Silahkan Klik link dibawah ini untuk mengetahui rincian perolehan jasa pelayanan anda. Link ini bersifat privasi dan tidak boleh dishare. Terima Kasih.<br><br><br>".$link,
                    "number"    => $value->phone
                ];
                Servant::send_wa("POST",$message);
            }

            DB::commit();
            $resp = [
                'success' => true,
                'message' => 'Pencairan berhasil diselesaikan!'
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

    public function export_excel($id)
	{
		return Excel::download(new JaspelExport($id), 'THP_'.$id.'.xlsx');
	}
    
    public function statistic_report($id)
	{
		$chart['statistik'] = new RemunChart;
        $pencairan = Pencairan_jasa_header::findOrFail($id);
        $data =  Pencairan_jasa_header::from('pencairan_jasa_header as ph')
                 ->join("pencairan_jasa as pj","pj.id_header","=","ph.id_cair_header")
                 ->join("employee as e","e.emp_id","=","pj.emp_id")
                 ->join("ms_unit as mu","mu.unit_id","=","e.unit_id_kerja")
                 ->select("mu.unit_name",DB::raw("sum(pj.total_brutto) as total_jasa"))
                 ->where([
                    "ph.id_cair_header" => $id,
                    "e.is_medis"        => "f"
                 ])
                 ->whereNotIn('e.jabatan_type', [16,17])
                 ->groupBy("mu.unit_name")
                //  ->limit("10")
                 ->get();
        $dataChart=$colors=[];
        foreach ($data as $key => $value) {
            $dataChart[$value->unit_name] = $value->total_jasa;
            $colors[] = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
        }
        $chart['statistik']->labels(array_keys($dataChart));
        $chart['statistik']->dataset('Jasa Pelayanan', 'bar', array_values($dataChart))
                            // ->color("rgb(216, 255, 119 )")
                            ->backgroundcolor($colors);
        $chart['statistik']->displayLegend(false);

        $chart['globalPendapatan']  = new RemunChart;
        $dataPercent   = DB::table("persentase_jasa")->where("id_cair",$id);
        $dataChart=$background=[];
        foreach ($dataPercent->get() as $key => $value) {
            $dataChart[$value->penjamin] = $value->persentase_jasa;
            $background[] = "rgb(".rand(100,250).",".rand(50,200).", ".rand(10,125).")";
        }
        
        $chart['globalPendapatan']->labels(array_keys($dataChart));
        $chart['globalPendapatan']->dataset('Pendapatan Global', 'pie', array_values($dataChart))
            ->color("rgb(216, 255, 119 )")
            ->backgroundcolor($background);

        return $this->themes("pencairan_jasa_header.printout.statistik",compact('chart'),'Statistik Jasa Pelayanan '.$pencairan->keterangan);
	}

    public function print_pdf($id)
    {
        set_time_limit(0);
        ini_set("memory_limit",-1);
        $cacheKey = 'laporan-' . $id;
        $data = Cache::get($cacheKey);
        if (!$data) {
            $data['potongan']   = Kategori_potongan::where("potongan_active","t")->get();
            $data['header']     = Pencairan_jasa_header::find($id);

            $data['detail'] = DB::select("SELECT x.golongan,x.emp_no,x.emp_name,x.nomor_rekening,
            x.total_brutto,
            json_arrayagg(
                json_object('kategori_id',x.kategori_potongan, 'potongan', x.total_potongan)
            )detail
            FROM (
                SELECT e.ordering_mode,e.emp_no,e.emp_name,e.golongan,pj.nomor_rekening,ph.kategori_potongan,pj.total_brutto,sum(pm.potongan_value)total_potongan,
                e.unit_id_kerja
                FROM pencairan_jasa pj
                join employee e on e.emp_id = pj.emp_id
                LEFT JOIN potongan_jasa_medis pm ON pm.pencairan_id = pj.id_cair
                LEFT JOIN potongan_penghasilan ph ON ph.id = pm.header_id
                where pj.id_header = '$id'
                group by e.ordering_mode,e.emp_no,e.emp_name,e.golongan,pj.nomor_rekening,ph.kategori_potongan,pj.total_brutto,e.unit_id_kerja
            )x
            GROUP BY x.ordering_mode,x.golongan,x.emp_no,x.emp_name,x.nomor_rekening,
            x.total_brutto,x.unit_id_kerja
            order by IFNULL(ordering_mode, '07'),x.unit_id_kerja,x.emp_name");
            Cache::put($cacheKey,$data,60);
        }
        // return view("pencairan_jasa_header.printout.print_pencairan",compact('data'));
        $pdf = PDF::loadview("pencairan_jasa_header.printout.print_pencairan",compact('data'))
               ->setPaper([0, 0, 750, 1500], 'landscape');
        // return $pdf->download('laporan-pegawai.pdf');
        return $pdf->stream();
        
    }

    /* public function file_excel($id)
    {
        set_time_limit(0);
        ini_set("memory_limit",-1);
        $cacheKey = 'laporan-' . $id;
        $data = Cache::get($cacheKey);
        if (!$data) {
            $data['potongan']   = Kategori_potongan::where("potongan_active","t")->get();
            $data['header']     = Pencairan_jasa_header::find($id);

            $data['detail'] = DB::select("SELECT x.unit_name,x.golongan,x.emp_no,x.emp_name,x.nomor_rekening,
            x.total_brutto,
            json_arrayagg(
                json_object('kategori_id',x.kategori_potongan, 'potongan', x.total_potongan)
            )detail
            FROM (
                SELECT e.ordering_mode,e.emp_no,e.emp_name,e.golongan,pj.nomor_rekening,ph.kategori_potongan,pj.total_brutto,sum(pm.potongan_value)total_potongan,
                e.unit_id_kerja,mu.unit_name
                FROM pencairan_jasa pj
                join employee e on e.emp_id = pj.emp_id
                join ms_unit mu on mu.unit_id = e.unit_id_kerja
                LEFT JOIN potongan_jasa_medis pm ON pm.pencairan_id = pj.id_cair
                LEFT JOIN potongan_penghasilan ph ON ph.id = pm.header_id
                where pj.id_header = '$id'
                group by e.ordering_mode,e.emp_no,e.emp_name,e.golongan,pj.nomor_rekening,ph.kategori_potongan,pj.total_brutto,e.unit_id_kerja,mu.unit_name
            )x
            GROUP BY x.ordering_mode,x.golongan,x.emp_no,x.emp_name,x.nomor_rekening,
            x.total_brutto,x.unit_id_kerja,x.unit_name
            order by IFNULL(ordering_mode, '07'),x.unit_id_kerja,x.emp_name");
            Cache::put($cacheKey,$data,60);
        }
        return view("pencairan_jasa_header.printout.file_excel",compact('data'));
    } */

    public function file_excel($id)
    {
        $pencairan =  Pencairan_jasa_header::find($id);
        return Excel::download(new PencairanExport($id), "".($pencairan->keterangan??"jaspel_$id").".xlsx");
    }
}
