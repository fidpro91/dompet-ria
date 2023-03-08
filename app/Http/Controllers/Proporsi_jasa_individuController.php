<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proporsi_jasa_individu;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;
use Illuminate\Support\Facades\DB;

class Proporsi_jasa_individuController extends Controller
{
    public $model   = "Proporsi_jasa_individu";
    public $folder  = "proporsi_jasa_individu";
    public $route   = "proporsi_jasa_individu";

    public $param = [
        'employee_id'   =>  'required',
        'komponen_id'   =>  'required',
        'jasa_bulan'   =>  'required',
        'is_used'   =>  '',
        'id_jaspel'   =>  ''
    ];
    public $defaultValue = [
        'proporsi_id'   =>  '',
        'employee_id'   =>  '',
        'komponen_id'   =>  '',
        'jasa_bulan'   =>  '',
        'is_used'   =>  'f',
        'id_jaspel'   =>  ''
    ];
    public function index()
    {
        return $this->themes($this->folder . '.index', null, $this);
    }

    public function get_dataTable(Request $request)
    {
        $data = Proporsi_jasa_individu::from("proporsi_jasa_individu as pi")
                ->where("is_used","f")
                ->join("employee as e","e.emp_id","=","pi.employee_id")
                ->join("ms_unit as mu","mu.unit_id","=","e.unit_id_kerja")
                ->select([
                    'proporsi_id',
                    'employee_id',
                    'e.emp_no',
                    'e.emp_name',
                    'mu.unit_name'
                ]);
        if ($request->komponen_id) {
            $data->where("pi.komponen_id",$request->komponen_id);
        }
        if ($request->bulan_jasa) {
            $data->where("pi.jasa_bulan",$request->bulan_jasa);
        }
        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('checkbox', function ($data) {
            $button = '<input type="checkbox" name="proporsi_id[]" value="'.$data->proporsi_id.'" />';
            return $button;
        })->editColumn('emp_no',function($data){
            $txt = $data->emp_no;
            if (!$data->emp_no) {
                $txt = '-';
            }
            return $txt;
        })->rawColumns(['checkbox']);
        return $datatables->make(true);
    }

    public function create()
    {
        $proporsi_jasa_individu = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('proporsi_jasa_individu'));
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
            Proporsi_jasa_individu::create($valid['data']);
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

    public function copy_data(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = Proporsi_jasa_individu::where([
                "id_jaspel" => $request->jasa_pelayanan 
            ]);
            if ($request->komponen_id2) {
                $data->where("komponen_id",$request->komponen_id2);
            }
            $input = [];
            foreach ($data->get() as $key => $value) {
                $input[$key] = [
                    'employee_id'   =>  $value->employee_id,
                    'komponen_id'   =>  $value->komponen_id,
                    'jasa_bulan'   =>  $request->bulan_skor,
                    'is_used'   =>  'f'
                ];
            }
            DB::table("proporsi_jasa_individu")->insert($input);
            DB::commit();
            $resp = [
                'success' => true,
                'message' => 'Data Berhasil Dicopy!'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            $resp = [
                'success' => false,
                'message' => 'Data Gagal Dicopy! <br>' . $e->getMessage()
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

    public function edit(Proporsi_jasa_individu $proporsi_jasa_individu)
    {
        return view($this->folder . '.form', compact('proporsi_jasa_individu'));
    }
    public function update(Request $request, Proporsi_jasa_individu $proporsi_jasa_individu)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        try {
            $data = Proporsi_jasa_individu::findOrFail($proporsi_jasa_individu->proporsi_id);
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
        $data = Proporsi_jasa_individu::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus!'
        ]);
    }

    public function clear_data(Request $request)
    {
        $data = Proporsi_jasa_individu::where([
            "is_used"       => "f",
            "jasa_bulan"    => $request->bulan_pelayanan
        ])->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dibersihkan!'
        ]);
    }

    public function insert_right(Request $request)
	{
		DB::beginTransaction();
        try {
            foreach ($request->emp_id as $key => $value) {
                $data[$key] = [
                    "employee_id" 		=> $value,
                    'komponen_id'       =>  $request->komponen_id,
                    'jasa_bulan'        =>  $request->bulan_pelayanan,
                ];
                Proporsi_jasa_individu::where([
                    "komponen_id"       => $request->komponen_id,
                    "jasa_bulan"        => $request->bulan_pelayanan,
                    "is_used"           => "f",
                    "employee_id" 		=> $value,
                ])->delete();
            }
            DB::table('proporsi_jasa_individu')->insert($data);
            DB::commit();
            $resp = [
                'success' => true,
                'message' => 'Data Berhasil Diupdate!'
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

	public function insert_left(Request $request)
	{
		DB::beginTransaction();
        try {
            foreach ($request->proporsi_id as $key => $value) {
                Proporsi_jasa_individu::findOrFail($value)->delete();
            }
            DB::commit();
            $resp = [
                'success' => true,
                'message' => 'Data Berhasil Diupdate!'
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
}
