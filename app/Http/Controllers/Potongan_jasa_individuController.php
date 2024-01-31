<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Potongan_jasa_individu;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;

class Potongan_jasa_individuController extends Controller
{
    public $model   = "Potongan_jasa_individu";
    public $folder  = "potongan_jasa_individu";
    public $route   = "potongan_jasa_individu";

    public $param = [
        'kategori_potongan'   =>  'required',
        'emp_id'   =>  'required',
        'potongan_type'   =>  'required',
        'potongan_value'   =>  'required',
        'pot_status'   =>  'required',
        'pot_note'   =>  '',
        'last_angsuran'   =>  '',
        'max_angsuran'   =>  ''
    ];
    public $defaultValue = [
        'pot_ind_id'   =>  '',
        'kategori_potongan'   =>  '',
        'emp_id'   =>  '',
        'potongan_type'   =>  '',
        'potongan_value'   =>  '',
        'pot_status'   =>  '',
        'pot_note'   =>  '',
        'last_angsuran'   =>  '0',
        'max_angsuran'   =>  ''
    ];
    public function index()
    {
        return $this->themes($this->folder . '.index', null, $this);
    }

    public function get_dataTable(Request $request)
    {
        $data = Potongan_jasa_individu::from("potongan_jasa_individu as pj")
                ->join("employee as e","e.emp_id","=","pj.emp_id")
                ->join("kategori_potongan as kp","kp.kategori_potongan_id","=","pj.kategori_potongan")
                ->select([
                    'pot_ind_id',
                    'e.emp_no',
                    'e.emp_name',
                    'kp.nama_kategori',
                    'pj.potongan_type',
                    'potongan_value',
                    'pot_note',
                    'last_angsuran',
                    'max_angsuran',
                    'pot_status'
                ]);
        
        if ($request->jenis_potongan) {
            $data->where("pj.kategori_potongan",$request->jenis_potongan);
        }

        if ($request->potongan_status) {
            $data->where("pot_status",$request->potongan_status);
        }
        
        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>", [
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route . ".edit", $data->pot_ind_id),
                "ajax-url"  => route($this->route . '.update', $data->pot_ind_id),
                "data-target"  => "page_potongan_jasa_individu"
            ]);

            $button .= Create::action("<i class=\"fas fa-trash\"></i>", [
                "class"     => "btn btn-danger btn-xs",
                "onclick"   => "delete_row(this)",
                "x-token"   => csrf_token(),
                "data-url"  => route($this->route . ".destroy", $data->pot_ind_id),
            ]);
            return $button;
        })->editColumn('potongan_type',function($data){
            if ($data->potongan_type == 1) {
                $txt = 'NOMINAL POTONGAN';
            }else {
                $txt = 'PERSENTASE POTONGAN';
            }
            return $txt;
        })->editColumn('pot_status',function($data){
            if ($data->pot_status == 't') {
                $txt = '<label class="badge badge-purple">AKTIF</label>';
            }else {
                $txt = '<label class="badge badge-danger">NON AKTIF</label>';
            }
            return $txt;
        })->rawColumns(['action','pot_status']);
        return $datatables->make(true);
    }

    public function create()
    {
        $potongan_jasa_individu = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('potongan_jasa_individu'));
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
            Potongan_jasa_individu::create($valid['data']);
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

    public function edit(Potongan_jasa_individu $potongan_jasa_individu)
    {
        return view($this->folder . '.form', compact('potongan_jasa_individu'));
    }
    public function update(Request $request, Potongan_jasa_individu $potongan_jasa_individu)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = Potongan_jasa_individu::findOrFail($potongan_jasa_individu->pot_ind_id);
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
        $data = Potongan_jasa_individu::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }
}
