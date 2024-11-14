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

Route::group(['middleware' => ['auth','client']], function(){
    // Route::get('/', [LoginController::class, 'login'])->name('login');
    Route::get('/index', function () {
        return view("mobile.index");
    }); 

    Route::get('/profil',"Mobile\ProfilController@index");
    Route::get('/profil/info/{id?}',"Mobile\ProfilController@load_info");

    Route::get('/jasa_pelayanan/detail/{id?}',"Mobile\JaspelController@detail");
    Route::get('/jasa_pelayanan',"Mobile\JaspelController@index");
    Route::get('jasa_pelayanan/monitoring_remun',"Mobile\JaspelController@monitoring_remun");
    Route::get('/jasa_pelayanan/skoring',"Mobile\JaspelController@skoring");
    Route::get('/jasa_pelayanan/point_medis/{id?}',"Mobile\JaspelController@point_medis");
});