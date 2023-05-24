<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tugas_tambahan;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;
use Illuminate\Support\Facades\Storage;

class Tugas_tambahanController extends Controller
{
    public $model   = "Tugas_tambahan";
    public $folder  = "tugas_tambahan";
    public $route   = "tugas_tambahan";

    public $param = [
        'nama_tugas'   =>  'required',
        'pemberi_tugas'   =>  'required',
        'nomor_sk'   =>  'required',
        'emp_id'   =>  'required',
        'tanggal_awal'   =>  'required',
        'tanggal_akhir'   =>  '',
        'deskripsi_tugas'   =>  '',
        'jabatan_tugas'   =>  'required',
        'created_at'   =>  '',
        'updated_at'   =>  '',
        'created_by'   =>  '',
        'file_sk'   =>  '',
        'is_active'   =>  'required'
    ];
    public $defaultValue = [
        'id'   =>  '',
        'nama_tugas'   =>  '',
        'pemberi_tugas'   =>  '',
        'nomor_sk'   =>  '',
        'emp_id'   =>  '',
        'tanggal_awal'   =>  '',
        'tanggal_akhir'   =>  '',
        'deskripsi_tugas'   =>  '',
        'jabatan_tugas'   =>  '',
        'created_at'   =>  '',
        'updated_at'   =>  '',
        'created_by'   =>  '',
        'file_sk'   =>  '',
        'is_active'   =>  't'
    ];
    public function index()
    {
        return $this->themes($this->folder . '.index', null, $this);
    }

    public function get_dataTable(Request $request)
    {
        $data = Tugas_tambahan::from( 'tugas_tambahan as tt' )
                ->join("employee as e1","e1.emp_id","=","tt.pemberi_tugas")
                ->join("employee as e2","e2.emp_id","=","tt.emp_id")
                ->join("detail_indikator as di","di.detail_id","=","tt.jabatan_tugas")
                ->select([
                    'id',
                    'nama_tugas',
                    'e1.emp_name as pemberi_tugas',
                    'nomor_sk',
                    'e2.emp_name as petugas',
                    'tanggal_awal',
                    'tanggal_akhir',
                    'deskripsi_tugas',
                    'di.detail_name as jabatan_tugas',
                    'di.skor',
                    'file_sk',
                    'is_active'
                ]);

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>", [
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route . ".edit", $data->id),
                "ajax-url"  => url($this->route.'/update_data'),
                "data-target"  => "page_tugas_tambahan",
                "data-method"  => "post"
            ]);
            $button .= Create::action("<i class=\"fas fa-trash\"></i>", [
                "class"     => "btn btn-danger btn-xs",
                "onclick"   => "delete_row(this)",
                "x-token"   => csrf_token(),
                "data-url"  => route($this->route . ".destroy", $data->id),
            ]);
            $button .= Create::action("<i class=\"fas fa-download\"></i>", [
                "class"     => "btn btn-secondary btn-xs",
                "onclick"   => "download_sk(this)"
            ]);
            return $button;
        })->editColumn('is_active',function($data){
            if ($data->is_active == 't') {
                $txt = '<span class="badge badge-info">Aktif</span>';
            }else{
                $txt = '<span class="badge badge-danger">Non Aktif</span>';
            }
            return $txt;
        })->rawColumns(['action','is_active']);
        return $datatables->make(true);
    }

    public function create()
    {
        $tugas_tambahan = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('tugas_tambahan'));
    }

    public function store(Request $request)
    {
        list($tgl1,$tgl2) = explode('-',$request->tanggal_tugas);
        $request['tanggal_awal'] = date('Y-m-d',strtotime($tgl1));
        $request['tanggal_akhir'] = date('Y-m-d',strtotime($tgl2));
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $valid['message']
            ]);
        }
        try {
            if ($request->file('file_sk')) {
                $image = $request->file('file_sk');
                $image->storeAs('public/uploads/sk_tugas', $image->hashName());
                $valid['data']['file_sk'] = $image->hashName();
            }
            Tugas_tambahan::create($valid['data']);
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
                "message"   => implode(',',$validator->errors()->all())
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

    public function edit(Tugas_tambahan $tugas_tambahan)
    {
        return view($this->folder . '.form', compact('tugas_tambahan'));
    }
    public function update_data(Request $request)
    {
        list($tgl1,$tgl2) = explode('-',$request->tanggal_tugas);
        $request['tanggal_awal'] = date('Y-m-d',strtotime($tgl1));
        $request['tanggal_akhir'] = date('Y-m-d',strtotime($tgl2));
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = Tugas_tambahan::findOrFail($request->id);
            if ($request->file('file_sk')) {
                //hapus old image
                Storage::disk('local')->delete('public/uploads/sk_tugas/'.$data->file_sk);
                //upload new image
                $image = $request->file('file_sk');
                $image->storeAs('public/uploads/sk_tugas', $image->hashName());
                $valid['data']['file_sk'] = $image;
            }
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
        $data = Tugas_tambahan::findOrFail($id);
        if ($data->file_sk) {
            Storage::disk('local')->delete('public/uploads/sk_tugas/'.$data->file_sk);
        }
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }
}
