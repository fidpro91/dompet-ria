<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ms_group;
use App\Models\Ms_menu;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;
use Illuminate\Support\Facades\DB;

class Ms_groupController extends Controller
{
    public $model   = "ms_group";
    public $folder  = "ms_group";
    public $route   = "ms_group";

    public $param = [
        'group_code'   =>  '',
        'group_name'   =>  'required',
        'group_active'   =>  'required',
        'group_type'   =>  'required'
    ];
    public $defaultValue = [
        'group_id'   =>  '',
        'group_code'   =>  '',
        'group_name'   =>  '',
        'group_active'   =>  '',
        'group_type'   =>  ''
    ];
    public function index()
    {
        return $this->themes($this->folder . '.index', null, $this);
    }

    public function get_dataTable(Request $request)
    {
        $data = ms_group::select([
            'group_id',
            'group_code',
            'group_name',
            'group_active'
        ]);

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>", [
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route . ".edit", $data->group_id),
                "ajax-url"  => route($this->route . '.update', $data->group_id),
                "data-target"  => "page_ms_group"
            ]);

            $button .= Create::action("<i class=\"fas fa-trash\"></i>", [
                "class"     => "btn btn-danger btn-xs",
                "onclick"   => "delete_row(this)",
                "x-token"   => csrf_token(),
                "data-url"  => route($this->route . ".destroy", $data->group_id),
            ]);
            return $button;
        })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function get_hak_akses(Request $request)
    {
        $group_id = $request->group_id;
        $data = Ms_menu::from("ms_menu as m")
                ->select([
                    'ga.group_id',
                    'm.menu_id',
                    'menu_code',
                    'menu_name'
                ])
                ->leftJoin("group_access as ga",function($leftJoin)use($group_id)
                {
                    $leftJoin->on('ga.menu_id', '=', 'm.menu_id');
                    $leftJoin->on('ga.group_id', '=', DB::raw("'".$group_id."'"));
                })
                ->orderBy("menu_code")
                ->distinct();
        $data->where([
            "menu_status"   => "t"
        ]);
        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('checkbox', function ($data) {
            $checked="";
            if ($data->group_id > 1) {
                $checked = "checked";
            }
            $button = '<input type="checkbox" name="menu_id[]" '.$checked.' value="'.$data->menu_id.'" />';
            return $button;
        })->rawColumns(['checkbox']);
        return $datatables->make(true);
    }

    public function create()
    {
        $ms_group = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('ms_group'));
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
        DB::beginTransaction();
        try {
            $save=ms_group::create($valid['data']);
            $id = $save->group_id;
            if ($request->menu_id) {
                $this->set_hak_akses($request->menu_id,$id);
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
                'message' => 'Data Gagal Disimpan! <br>' . $e->getMessage()
            ];
        }
        return response()->json($resp);
    }

    private function set_hak_akses($menu,$group_id)
    {
        DB::table("group_access")->where("group_id",$group_id)->delete();
        $akses=[];
        foreach ($menu as $key => $value) {
            $akses[$key] = [
                "group_id"      => $group_id,
                "menu_id"       => $value
            ];
        }
        DB::table("group_access")->insert($akses);
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

    public function edit(ms_group $ms_group)
    {
        return view($this->folder . '.form', compact('ms_group'));
    }
    public function update(Request $request, ms_group $ms_group)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        DB::beginTransaction();
        try {
            $data = ms_group::findOrFail($ms_group->group_id);
            $data->update($valid['data']);
            if ($request->menu_id) {
                $this->set_hak_akses($request->menu_id,$request->group_id);
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

    public function destroy($id)
    {
        $data = ms_group::findOrFail($id);
        DB::beginTransaction();
        try {
            DB::table("group_access")->where("group_id",$id)->delete();
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
}
