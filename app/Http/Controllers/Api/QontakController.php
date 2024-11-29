<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Libraries\Qontak;
use App\Models\Employee;
use App\Models\Performa_index;
use App\Models\Range_det_indikator;
use App\Models\Rekap_ijin;
use App\Models\Table_rekap_absen;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class QontakController extends Controller
{
    public function send_otp(Request $request) {
        $message = Qontak::sendOTP($request->number,$request->name,$request->code);
        return ($message);
    }
}