<?php

namespace App\Http\Controllers;

use App\Models\Detail_tindakan_medis;
use App\Models\Employee;
use App\Models\Ms_reff;
use App\Models\Point_medis;
use Illuminate\Http\Request;
use App\Models\Repository_download;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;
use Illuminate\Support\Facades\DB;

class Repository_downloadController extends Controller
{
    public $model   = "Repository_download";
    public $folder  = "repository_download";
    public $route   = "repository_download";

    public $param = [
        'download_date'   =>  'required',
        'bulan_jasa'   =>  '',
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
        $data = Repository_download::with('hasCopy')
                ->select([
                    'id',
                    'download_date',
                    'download_no',
                    'bulan_pelayanan',
                    'periode_awal',
                    'periode_akhir',
                    'group_penjamin',
                    'jenis_pembayaran',
                    'download_by',
                    'is_used',
                    'total_data',
                    'skor_eksekutif',
                    'skor_non_eksekutif'
                ]);
        if ($request->is_used) {
            $data->where("is_used",$request->is_used);
        }

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button="";
            if ($data->is_used == 'f') {
                $button = Create::action("<i class=\"fas fa-trash\"></i>", [
                    "class"     => "btn btn-danger btn-xs",
                    "onclick"   => "delete_row(this)",
                    "x-token"   => csrf_token(),
                    "data-url"  => route($this->route . ".destroy", $data->id),
                ]);
            }

            $button .= Create::action("<i class=\"fas fa-eraser\"></i>", [
                "class"     => "btn btn-pink btn-xs",
                "onclick"   => "delete_copy($data->id)"
            ]);
            
            $button .= Create::action("<i class=\"fas fa-copy\"></i>", [
                "class"     => "btn btn-info btn-xs",
                "onclick"   => "copy_data($data->id)"
            ]);

            $button .= Create::action("<i class=\"fas fa-eye\"></i>", [
                "class"     => "btn btn-purple btn-xs",
                "onclick"   => "show_data($data->id)"
            ]);

            return $button;
        })->addColumn('jml_jaspel', function ($data) {
            return $data->hasCopy->count();
        })
        ->editColumn('periode_awal',function($data){
            return date_indo($data->periode_awal).'/'.date_indo($data->periode_akhir);
        })
        ->editColumn('bulan_pelayanan',function($data){
            return get_namaBulan($data->bulan_pelayanan);
        })
        ->editColumn('total_data',function($data){
            $html = "<div>
                        <b>
                            Total Data : $data->total_data <br>
                            Total Eksekutif : ".convert_currency2($data->skor_eksekutif)." <br>
                            Total Non Eksekutif : ".convert_currency2($data->skor_non_eksekutif)."
                        </b>
                    </div>";
            return $html;
        })
        ->editColumn('group_penjamin',function($data){
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
        })->rawColumns(['action','total_data']);
        return $datatables->make(true);
    }

    public function create()
    {
        $repository_download = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('repository_download'));
    }

    public function copy_point(Request $request)
    {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        DB::disableQueryLog();
        try {
            $repoDownload = Repository_download::findOrFail($request->id);
            Point_medis::where([
                "repo_id"   => $request->id,
                "is_usage"  => "f"
            ])->delete();
            $where="";
            if ($request->group_penjamin) {
                $where = "and dm.penjamin_id in (".implode(',',$request->group_penjamin).")";
            }
            DB::select("
                INSERT INTO `point_medis`(`bulan_jaspel`, `bulan_pelayanan`, `id_tindakan`, `penjamin`, `skor`, `is_eksekutif`, `is_usage`, `employee_id`, `jenis_tagihan`, `repo_id`, `is_copy`)
                SELECT '$request->bulan_jaspel',rd.bulan_pelayanan,dm.tindakan_id,dm.penjamin_id,dm.skor_jasa,dm.unit_vip,'f',COALESCE (NULLIF(dm.id_dokter, ''), 0 ),dm.jenis_tagihan,
                dm.repo_id,'t'
                FROM detail_tindakan_medis as dm
                join repository_download as rd on dm.repo_id = rd.id
                where dm.repo_id = $request->id $where
            ");

            DB::table("skor_pegawai")->where("bulan_update",$repoDownload->bulan_pelayanan)->update([
                "prepare_remun"         => "t",
                "prepare_remun_month"   => $request->bulan_jaspel
            ]);

            Repository_download::findOrFail($request->id)->update([
                "is_used"   => "f"
            ]);

            $resp = [
                "code"      => 200,
                "message"   => "Data berhasil dicopy"
            ];
            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            $resp = [
                'code'      => 201,
                'message'   => 'Data Gagal Disimpan! <br>' . $e->getMessage()
            ];
        }

        return response()->json($resp);
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
        Point_medis::where('repo_id',$id)->delete();
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }

    public function delete_copy($id)
    {
        Point_medis::where([
            'repo_id'   => $id,
            'is_usage'  => 'f'
        ])->delete();

        Repository_download::find($id)->update([
            "is_used"   => "t"
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }
}
