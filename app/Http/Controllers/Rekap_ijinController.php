<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rekap_ijin;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;
use Carbon\Carbon;

class Rekap_ijinController extends Controller
{
    public $model   = "Rekap_ijin";
    public $folder  = "rekap_ijin";
    public $route   = "rekap_ijin";

    public $param = [
        'id'   =>  'required',
        'nip'   =>  'required',
        'nama_pegawai'   =>  'required',
        'jenis_ijin'   =>  '',
        'tipe_ijin'   =>  '',
        'tgl_mulai'   =>  '',
        'tgl_selesai'   =>  '',
        'lama_ijin'   =>  '',
        'keterangan'   =>  '',
        'created_at'   =>  '',
        'updated_at'   =>  ''
    ];
    public $defaultValue = [
        'id'   =>  '',
        'nip'   =>  '',
        'nama_pegawai'   =>  '',
        'jenis_ijin'   =>  '',
        'tipe_ijin'   =>  '',
        'tgl_mulai'   =>  '',
        'tgl_selesai'   =>  '',
        'lama_ijin'   =>  '',
        'keterangan'   =>  '',
        'created_at'   =>  '',
        'updated_at'   =>  ''
    ];
    public function index()
    {
        return $this->themes($this->folder . '.index', null, $this);
    }

    public function get_dataTable(Request $request)
    {
        
        list($tgl1, $tgl2) = explode(' - ', $request->periode_awal);        
        $startDate = Carbon::createFromFormat('m/d/Y', $tgl1)->format('Y-m-d');
        $endDate = Carbon::createFromFormat('m/d/Y', $tgl2)->format('Y-m-d');
        
        $data = Rekap_ijin::select(
            [
                'id',
                'nip',
                'nama_pegawai',
                'jenis_ijin',
                'tipe_ijin',
                'tgl_mulai',
                'tgl_selesai',
                'lama_ijin',
                'keterangan',
                'created_at',
                'updated_at'
            ]
        );

        $data->whereRaw("DATE_FORMAT(tgl_mulai, '%Y') = ?", [$request->tahun_update]);
        $data->whereRaw("tgl_mulai BETWEEN ? AND ?", [$startDate, $endDate]);
        if($request->lama !=null ){
            if ($request->lama == '1') {               
                $data->whereRaw("lama_ijin <= ?", [3]);
            } else {                
                $data->whereRaw("lama_ijin > ?", [3]);
            }
        }

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            
        })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function create()
    {
        $rekap_ijin = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('rekap_ijin'));
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
            Rekap_ijin::create($valid['data']);
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

    public function edit(Rekap_ijin $rekap_ijin)
    {
        return view($this->folder . '.form', compact('rekap_ijin'));
    }
    public function update(Request $request, Rekap_ijin $rekap_ijin)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = Rekap_ijin::findOrFail($rekap_ijin->id);
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
        $data = Rekap_ijin::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }
}
