<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class DompetController extends Controller
{
    public function get_pegawai (Request $request) {
        $data = Employee::where("emp_no",$request->nip)->first();

        if ($data) {
            $resp = [
                "code"      => 200,
                "message"   => "OK",
                "data"      => [
                    "nip"   => $data->emp_no,
                    "nama"  => $data->emp_name,
                    "unit_kerja"    => $data->unit->unit_name
                ]
            ];
        }else {
            $resp = [
                "code"      => 201,
                "message"   => "Data tidak ditemukan"
            ];
        }

        return response()->json($resp);
    }
}
