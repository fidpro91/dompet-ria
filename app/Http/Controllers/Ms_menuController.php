<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ms_menu;
use Illuminate\Support\Facades\Validator;
use DataTables;
use fidpro\builder\Create;
use Illuminate\Support\Facades\Cache;

class Ms_menuController extends Controller
{
    public $model   = "Ms_menu";
    public $folder  = "ms_menu";
    public $route   = "ms_menu";

    public $param = [
        'menu_code'   =>  'required',
        'menu_name'   =>  'required',
        'menu_url'   =>  '',
        'menu_parent_id'   =>  '',
        'menu_status'   =>  '',
        'menu_icon'   =>  '',
        'slug'   =>  ''
    ];
    public $defaultValue = [
        'menu_id'   =>  '',
        'menu_code'   =>  '',
        'menu_name'   =>  '',
        'menu_url'   =>  '',
        'menu_parent_id'   =>  '',
        'menu_status'   =>  '',
        'menu_icon'   =>  '',
        'slug'   =>  ''
    ];
    public function index()
    {
        return $this->themes($this->folder . '.index', null, $this);
    }

    public function get_dataTable(Request $request)
    {
        $data = Ms_menu::select([
            'menu_id',
            'menu_code',
            'menu_name',
            'menu_url',
            'menu_parent_id',
            'menu_status',
            'menu_icon',
            'slug'
        ]);

        $datatables = DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = Create::action("<i class=\"fas fa-edit\"></i>", [
                "class"     => "btn btn-primary btn-xs",
                "onclick"   => "set_edit(this)",
                "data-url"  => route($this->route . ".edit", $data->menu_id),
                "ajax-url"  => route($this->route . '.update', $data->menu_id),
                "data-target"  => "page_ms_menu"
            ]);

            $button .= Create::action("<i class=\"fas fa-trash\"></i>", [
                "class"     => "btn btn-danger btn-xs",
                "onclick"   => "delete_row(this)",
                "x-token"   => csrf_token(),
                "data-url"  => route($this->route . ".destroy", $data->menu_id),
            ]);
            return $button;
        })->rawColumns(['action']);
        return $datatables->make(true);
    }

    public function create()
    {
        $ms_menu = (object)$this->defaultValue;
        return view($this->folder . '.form', compact('ms_menu'));
    }

    public function store(Request $request)
    {
        $valid = $this->form_validasi($request->all());
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }
        $save = Ms_menu::create($valid['data']);
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan!'
        ]);
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

    public function edit(Ms_menu $ms_menu)
    {
        return view($this->folder . '.form', compact('ms_menu'));
    }
    public function update(Request $request, Ms_menu $ms_menu)
    {
        $valid = $this->form_validasi($request->all());
        Cache::forget('menuCache');
        if ($valid['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $this->form_validasi($request->all())['message']
            ]);
        }

        $data = Ms_menu::findOrFail($ms_menu->menu_id);
        $data->update($valid['data']);
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diudapte!'
        ]);
    }

    public function destroy($id)
    {
        $data = Ms_menu::findOrFail($id);
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diudapte!'
        ]);
    }
}
