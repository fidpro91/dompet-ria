<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('prestige/get_rekap_presensi_absen','PrestigeController@get_rekap_presensi_absen');
Route::post('prestige/insert_kedisiplinan','PrestigeController@insert_kedisiplinan');
Route::post('prestige/rekap_ijin',"PrestigeController@get_ijin_pegawai");
Route::post('qontak/send_otp',"QontakController@send_otp");

Route::get('/get_pegawai',"DompetController@get_pegawai");