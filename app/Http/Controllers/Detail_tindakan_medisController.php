<?php

namespace App\Http\Controllers;

use App\Libraries\Servant;
use Illuminate\Http\Request;
use App\Models\Detail_tindakan_medis;
use App\Models\Repository_download;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class Detail_tindakan_medisController extends Controller
{
    public $model   = "Detail_tindakan_medis";
    public $folder  = "detail_tindakan_medis";
    public $route   = "detail_tindakan_medis";

    public $param = [
        'jp_medis_id'   =>  '',
        'tanggal_tindakan'   =>  '',
        'nama_tindakan'   =>  '',
        'tarif_tindakan'   =>  '',
        'id_klasifikasi_jasa'   =>  '',
        'klasifikasi_jasa'   =>  '',
        'percentase_jasa'   =>  '',
        'skor_jasa'   =>  '',
        'qty_tindakan'   =>  '',
        'px_norm'   =>  '',
        'px_name'   =>  '',
        'unit_layanan'   =>  '',
        'unit_layanan_id'   =>  '',
        'visit_id'   =>  '',
        'nip'   =>  '',
        'nama_dokter'   =>  '',
        'unit_vip'   =>  '',
        'penjamin_id'   =>  '',
        'nama_penjamin'   =>  '',
        'status_bayar'   =>  '',
        'tanggal_import'   =>  '',
        'billing_id'   =>  '',
        'status_jasa'   =>  '',
        'jasa_tindakan_bulan'   =>  '',
        'repo_id'   => 'required'
    ];

    public $defaultValue = [
        'tindakan_id'   =>  '',
        'jp_medis_id'   =>  '',
        'tanggal_tindakan'   =>  '',
        'nama_tindakan'   =>  '',
        'tarif_tindakan'   =>  '',
        'id_klasifikasi_jasa'   =>  '',
        'klasifikasi_jasa'   =>  '',
        'percentase_jasa'   =>  '',
        'skor_jasa'   =>  '',
        'qty_tindakan'   =>  '',
        'px_norm'   =>  '',
        'px_name'   =>  '',
        'unit_layanan'   =>  '',
        'unit_layanan_id'   =>  '',
        'visit_id'   =>  '',
        'nip'   =>  '',
        'nama_dokter'   =>  '',
        'unit_vip'   =>  '',
        'penjamin_id'   =>  '',
        'nama_penjamin'   =>  '',
        'status_bayar'   =>  '',
        'tanggal_import'   =>  '',
        'billing_id'   =>  '',
        'status_jasa'   =>  '3',
        'jasa_tindakan_bulan'   =>  '',
        'repo_id'   => ''
    ];
    
    public function index()
    {
        return $this->themes($this->folder . '.index', null, $this);
    }

    public function get_dataTable(Request $request)
    {
        $data = Detail_tindakan_medis::select([
            'tindakan_id',
            'jp_medis_id',
            'tanggal_tindakan',
            'nama_tindakan',
            'tarif_tindakan',
            'id_klasifikasi_jasa',
            'klasifikasi_jasa',
            'percentase_jasa',
            'skor_jasa',
            'qty_tindakan',
            'px_norm',
            'px_name',
            'unit_layanan',
            'unit_layanan_id',
            'visit_id',
            'nip',
            'nama_dokter',
            'unit_vip',
            'penjamin_id',
            'nama_penjamin',
            'status_bayar',
            'tanggal_import',
            'billing_id',
            'status_jasa',
            'jasa_tindakan_bulan'
        ]);

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>", [
                "class"     => "btn btn-primary btn-sm",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route . ".edit", $data->tindakan_id),
                "ajax-url"  => route($this->route . '.update', $data->tindakan_id),
                "data-target"  => "page_detail_tindakan_medis"
            ]);

            $button .= Create::action("<i class=\"fas fa-trash\"></i>", [
                "class"     => "btn btn-danger btn-sm",
                "onclick"   => "delete_row(this)",
                "x-token"   => csrf_token(),
                "data-url"  => route($this->route . ".destroy", $data->tindakan_id),
            ]);
            return $button;
        })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function create()
    {
        $detail_tindakan_medi = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('detail_tindakan_medi'));
    }

    public function store(Request $request)
    {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            DB::disableQueryLog();
            $input = Cache::get('inputCache'); 
            $bill = Cache::get('billCache');
            list($tanggal1,$tanggal2) = explode('-',$input['periode_tindakan']);
            $tanggal1 = date("Y-m-d",strtotime($tanggal1));
            $tanggal2 = date("Y-m-d",strtotime($tanggal2));
            $penjamin = "";
            if (!empty($input['surety_id'])) {
                $penjamin = json_encode($input['surety_id']);
            }
            $nomor = Servant::generate_code_transaksi([
                "text"	=> "DOWNLOAD/NOMOR/".date("d.m.Y"),
                "table"	=> "repository_download",
                "column"	=> "download_no",
                "delimiterFirst" => "/",
                "delimiterLast" => "/",
                "limit"	=> "2",
                "number"	=> "-1",
            ]);
            $repoDownload = [
                'download_date'     =>  date('Y-m-d'),
                'bulan_jasa'        =>  $input['bulan_jasa'],
                'bulan_pelayanan'   =>  $input['bulan_pelayanan'],
                'periode_awal'      =>  $tanggal1,
                'periode_akhir'     =>  $tanggal2,
                'group_penjamin'    =>  $penjamin,
                'jenis_pembayaran'  =>  $input['jenis_pembayaran'],
                'download_by'       =>  Auth::user()->id,
                "download_no"       => $nomor
            ];
            Repository_download::create($repoDownload);
            $repoId = Repository_download::latest()->first()->id;
            $bill = array_map(function ($arr) use ($repoId) {
				return $arr + ['repo_id' => $repoId];
			}, $bill);
            foreach (array_chunk($bill,1000) as $t)  
            {
                DB::table('detail_tindakan_medis')->insert($t); 
            }
            // DB::table("Detail_tindakan_medis")->insert();
            /* $skor = Cache::get('skorCache');
            DB::table('skor_per_bulan')->where([
                "bulan"				=> $input["bulan_pelayanan"],
                "bulan_jasa_cair"	=> $input['bulan_jasa'],
                "is_used"			=> 'f'
            ])->delete();
            DB::table('skor_per_bulan')->insert($skor); */
            $dataSkor = DB::select("
                select sp.* from skor_pegawai sp
                left join employee_off eo on sp.emp_id = eo.emp_id and ('".$input['bulan_pelayanan']."' between eo.bulan_jasa_awal and eo.bulan_jasa_akhir)
                WHERE sp.bulan_update = '".$input['bulan_pelayanan']."' AND eo.emp_id IS NULL
            ");
            foreach ($dataSkor as $key => $value) {
                DB::table("skor_pegawai")->where("id",$value->id)->update([
                    "prepare_remun"         => "t",
                    "prepare_remun_month"   => $input['bulan_jasa']
                ]);
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

    public function edit(Detail_tindakan_medis $detail_tindakan_medi)
    {
        return view($this->folder . '.form', compact('detail_tindakan_medi'));
    }
    public function update(Request $request, Detail_tindakan_medis $detail_tindakan_medi)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }

        $data = Detail_tindakan_medis::findOrFail($detail_tindakan_medi->tindakan_id);
        $data->update($valid['data']);
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diudapte!'
        ]);
    }

    public function destroy($id)
    {
        $data = Detail_tindakan_medis::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diudapte!'
        ]);
    }

    public function kroscek_tindakan(Request $request)
	{
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        list($tanggal1,$tanggal2) = explode('-',$request->periode_tindakan_kroscek);
        $filter = [
			"tanggal1"		=> date("Y-m-d",strtotime($tanggal1)),
			"tanggal2"		=> date("Y-m-d",strtotime($tanggal2)),
		];
        $datajaspelDetail  	= Servant::connect_simrs("POST",'kroscek_billing',json_encode($filter));
        $datajaspelDetail   = json_decode($datajaspelDetail);
        return $this->themes("detail_tindakan_medis.bill_not_mapp",['data'=>($datajaspelDetail->response??null)],"Data Tindakan Belum Dimapping");
    }

    public function get_data_simrs(Request $request)
	{
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        $input = (array) $request->all();
        Cache::forget('inputCache');
        Cache::forget('billCache');
        Cache::forget('skorCache');
        Cache::forget('employeeOffCache');
        Cache::forget('errorDownloadCache');
        Cache::add('inputCache', $input, 6000);
        list($tanggal1,$tanggal2) = explode('-',$input['periode_tindakan']);
		$penjamin = $request->surety_id;
		$filter = [
			"tanggalawal"		=> date("Y-m-d",strtotime($tanggal1)),
			"tanggalakhir"		=> date("Y-m-d",strtotime($tanggal2)),
			"jenis_pembayaran"	=> $input['jenis_pembayaran'],
		];
		if (!empty($penjamin)) {
			$filter = array_merge($filter,[
				"surety_id" => $penjamin
			]);
		}
		$datajaspelDetail  	= Servant::connect_simrs("POST",'get_tindakan',json_encode($filter));
		$datajaspelDetail	= json_decode($datajaspelDetail);
		DB::beginTransaction();
        $dokterFail=$billFail=[];
        try {
            /* DB::table('detail_tindakan_medis')->where([
                "bulan_pelayanan"	    => $input["bulan_pelayanan"],
                "jasa_tindakan_bulan"	=> $input['bulan_jasa'],
                "jp_medis_id"           => null
            ])->delete(); */
            $tindakanMedis=[];
            foreach ($datajaspelDetail->response as $xx => $r) {
                //cek apakah dokter ada di sistem
                $isDokter=DB::table('employee')->where("emp_nip",trim($r->employee_nip))->count();
                if($isDokter == 0){
                    $dokterFail[$xx] = [
                        "nip"   => trim($r->employee_nip),
                        "name"  => trim($r->namapelaksana),
                        "unit_layanan"          => $r->unit_name,
                        "id_kunjungan"          => $r->visit_id
                    ];
                }
                $tindakanMedis[$xx] = [
                    "tanggal_tindakan"		=> date("Y-m-d",strtotime($r->visit_end_date)),
                    "tanggal_import"		=> date('Y-m-d'),
                    "jasa_tindakan_bulan"	=> $input['bulan_jasa'],
                    "nama_tindakan"			=> addslashes($r->bill_name),
                    "tarif_tindakan"		=> $r->tarif,
                    "id_klasifikasi_jasa"	=> $r->id_klasifikasi_jasa,
                    "klasifikasi_jasa"		=> $r->klasifikasi_jasa,
                    "percentase_jasa"		=> (($r->is_vip=='t')?$r->percentase_eksekutif:$r->percentase_non_eksekutif),
                    "skor_jasa"				=> (($r->is_vip=='t')?$r->skor_eksekutif:$r->skor_noneksekutif),
                    "qty_tindakan"			=> $r->billing_qty,
                    "px_norm"				=> $r->px_norm,
                    "px_name"				=> addslashes($r->px_name),
                    "unit_layanan"			=> $r->unit_name,
                    "unit_layanan_id"		=> $r->unit_id,
                    "visit_id"				=> $r->visit_id,
                    "nip"					=> trim($r->employee_nip),
                    "nama_dokter"			=> trim($r->namapelaksana),
                    "unit_vip"				=> $r->is_vip,
                    "penjamin_id"			=> $r->surety_id,
                    "billing_id"			=> $r->billing_id,
                    "nama_penjamin"			=> $r->surety_name,
                    "status_bayar"			=> $r->status_bayar,
                    "bulan_pelayanan"		=> $input["bulan_pelayanan"],
                    "jenis_tagihan"			=> $input["jenis_pembayaran"],
                ];
            }
            
            Cache::add('billCache', $tindakanMedis, 6000);
            $dataSkor = DB::select("
                select sp.* from skor_pegawai sp
                left join employee_off eo on sp.emp_id = eo.emp_id and ('".$input['bulan_pelayanan']."' between eo.bulan_jasa_awal and eo.bulan_jasa_akhir)
                WHERE sp.bulan_update = '".$input['bulan_pelayanan']."' AND eo.emp_id IS NULL
            ");
            if ($dataSkor) {
                $skor=[];
                foreach ($dataSkor as $x => $value) {
                    $skor[$x] = [
                        "employee_id" => $value->emp_id,
                        "skor"		  => $value->total_skor,
                        "bulan"		  => $value->bulan_update,
                        "bulan_jasa_cair"	=> $input['bulan_jasa']
                    ];
                }
                Cache::add('skorCache', $skor, 6000);
                Cache::remember('employeeOffCache', 6000, function () use($input){
                    return DB::table("employee_off as eo")
                    ->join("employee as e","e.emp_id","=","eo.emp_id")
                    ->join("ms_unit as mu","e.unit_id_kerja","=","mu.unit_id")
                    ->select(["e.emp_nip","e.emp_name","mu.unit_name as unit_kerja"])
                    ->whereRaw("('".$input['bulan_pelayanan']."' between bulan_jasa_awal and bulan_jasa_akhir)")->get();
                });
            }else{
                $resp = [
                    'success' => false,
                    'message' => 'Data skor pegawai bulan '.$input['bulan_pelayanan'].' tidak ditemukan'
                ];
                return response()->json($resp);
            }
            $errorDownload = [
                "dokterFail"    => array_values(array_unique($dokterFail,SORT_REGULAR)),
                "billFail"      => array_values($billFail)
            ];
            Cache::add('errorDownloadCache', $errorDownload, 6000);
            DB::commit();
            $resp = [
                'success' => true,
                'message' => 'Data berhasil didownload'
            ];
        }catch(\Exception $e){
            DB::rollback();
            $resp = [
                'success' => false,
                'message' => 'Data gagal didownload! <br>'.$e->getMessage()
            ];
        }
        return response()->json($resp);
	}

    public function get_data_download(){
        return $this->themes("detail_tindakan_medis.data_download",null,$this);
    }
    
    public function get_error($type){
        if ($type == 1) {
            $data = Cache::get('employeeOffCache');
        }elseif ($type==2) {
            $data = Cache::get('errorDownloadCache')['dokterFail'];
        }
        $table = \fidpro\builder\Bootstrap::tableData($data,["class"=>"table table-bordered"]);
        return $table;
    }

    public function set_mapping_bill($id,$klasifikasi=0){
        $post = [
			"bill_id"		=> $id,
			"id_jasa"		=> $klasifikasi,
		];
        $response = Servant::connect_simrs("POST",'mapping_billing',json_encode($post));
        return ($response);
    }
}
