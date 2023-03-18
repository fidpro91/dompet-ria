<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Diklat;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;
use Illuminate\Support\Facades\Storage;

class DiklatController extends Controller
{
    public $model   = "Diklat";
    public $folder  = "diklat";
    public $route   = "diklat";

    public $param = [
        'dari_tanggal'   =>  'required',
        'sampai_tanggal'   =>  'required',
        'judul_pelatihan'   =>  'required',
        'penyelenggara'   =>  'required',
        'indikator_skor'   =>  '',
        'peserta_id'   =>  'required',
        'lokasi_pelatihan'   =>  '',
        'sertifikat_file'   =>  ''
    ];
    public $defaultValue = [
        'id'   =>  '',
        'dari_tanggal'   =>  '',
        'sampai_tanggal'   =>  '',
        'judul_pelatihan'   =>  '',
        'penyelenggara'   =>  '',
        'indikator_skor'   =>  '',
        'peserta_id'   =>  '',
        'lokasi_pelatihan'   =>  '',
        'sertifikat_file'   =>  ''
    ];
    public function index()
    {
        return $this->themes($this->folder . '.index', null, $this);
    }

    public function get_dataTable(Request $request)
    {
        $data = Diklat::from( 'diklat as dk' )
                ->join("employee as e","e.emp_id","=","dk.peserta_id")
                ->join("detail_indikator as di","di.detail_id","=","dk.indikator_skor")
                ->select([
                    'id',
                    'dari_tanggal',
                    'sampai_tanggal',
                    'judul_pelatihan',
                    'penyelenggara',
                    'di.detail_name',
                    'di.skor',
                    'e.emp_name',
                    'lokasi_pelatihan',
                    'sertifikat_file'
                ])->get();

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>", [
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route . ".edit", $data->id),
                "ajax-url"  => url($this->route . '/update_data'),
                "data-target"  => "page_diklat",
                "data-method"  => "post"
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
        $diklat = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('diklat'));
    }

    public function store(Request $request)
    {
        list($tgl1,$tgl2) = explode('-',$request->tanggal_pelatihan);
        $request['dari_tanggal'] = date('Y-m-d',strtotime($tgl1));
        $request['sampai_tanggal'] = date('Y-m-d',strtotime($tgl2));
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $valid['message']
            ]);
        }
        try {
            if ($request->file('sertifikat_file')) {
                $image = $request->file('sertifikat_file');
                $image->storeAs('public/uploads/sertifikat', $image->hashName());
                $valid['data']['sertifikat_file'] = $image->hashName();
            }
            Diklat::create($valid['data']);
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

    public function edit(Diklat $diklat)
    {
        return view($this->folder . '.form', compact('diklat'));
    }

    public function update_data(Request $request)
    {
        list($tgl1,$tgl2) = explode('-',$request->tanggal_pelatihan);
        $request['dari_tanggal'] = date('Y-m-d',strtotime($tgl1));
        $request['sampai_tanggal'] = date('Y-m-d',strtotime($tgl2));
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = Diklat::findOrFail($request->id);
            if ($request->file('sertifikat_file')) {
                //hapus old image
                Storage::disk('local')->delete('public/uploads/sertifikat/'.$data->sertifikat_file);
                //upload new image
                $image = $request->file('sertifikat_file');
                $image->storeAs('public/uploads/sertifikat', $image->hashName());
                $valid['data']['sertifikat_file'] = $image;
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
        $data = Diklat::findOrFail($id);
        if ($data->sertifikat_file) {
            Storage::disk('local')->delete('public/uploads/sertifikat/'.$data->sertifikat_file);
        }
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }
}
