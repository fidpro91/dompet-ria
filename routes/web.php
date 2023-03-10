<?php

use App\Http\Controllers\Builder\Docs\Example;
use App\Models\Ms_menu;
use Illuminate\Support\Facades\Route;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\LoginController;
use App\Libraries\Servant;
use Illuminate\Support\Facades\Config;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::post('login/login_act', 'LoginController@actionlogin');
// Route::post('login','LoginController@actionlogin');
Route::post('actionlogin', [LoginController::class, 'actionlogin'])->name('actionlogin');
Route::get('logout', [LoginController::class, 'actionlogout']);
Route::get('login/reload_capcha', [LoginController::class, 'reload_capcha']);
Route::get('/', [LoginController::class, 'login'])->name('login');

Route::get('/tes_package', function () {
    // echo config('app.name');
    /* $data=DB::table("users")->get();
    ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
    foreach ($data as $key => $value) {
        $value->password = bcrypt($value->password_decrypt);
        $input = (array) $value;
        DB::table("users")->where("id",$value->id)->update($input);
    }
    die; */
    $nomor = Servant::generate_code_transaksi([
        "text"	=> "INSENT/NOMOR/".date("d.m.Y"),
        "table"	=> "jasa_pelayanan",
        "column"	=> "no_jasa",
        "delimiterFirst" => "/",
        "delimiterLast" => "/",
        "limit"	=> "2",
        "number"	=> "-1",
    ]);
    print_r($nomor);
    die;
    $data = function(){
        return "tes";
    };
    if (is_callable($data)) {
        return 'ini fungsi';
    }
    print_r(bcrypt('123456'));
    die;
    // Cache::store('file')->get('foo');
    /* $tes = DB::table("employee_off")->whereRaw("('02-2023' between bulan_jasa_awal and bulan_jasa_akhir)")->get();
    print_r($tes);
    die;
    Cache::add('tes', '123', 60);
    print_r(Cache::store('file')->get('tes'));
    die; */
    // echo Servant::doSomethingUseful();
    /* print_r(bcrypt('admin@mail.com'));
    die;
    $hasil = Ms_menu::where('menu_parent_id', '0')->get();
    print_r($hasil);die; */
    $hasil = Schema::getColumnListing('ms_group');
    $results = DB::select('SHOW FIELDS FROM ms_group');
    foreach ($results as $x => $rs) {
        echo $rs->Null;
    }
    // print_r($row);
    /* echo(Breadcrumbs::render('ms_group'));
    $hasil = \fidpro\builder\Create::input("nama_pegawai")->render();
    echo ($hasil);
    $hasil = \fidpro\builder\Create::dropDown("pegawai",[
        "data" => [
            "model"     => "Models_builder\Employee",
            "custom"    => "tes_data",
            "column"    => ["emp_id","emp_name"]
        ]
    ])->render("group","pegawai");
    print_r ($hasil); */
});
Route::get('/builder/example/form_basic', [Example::class, 'form_basic']);
Route::get('/builder/example/form_widget', [Example::class, 'form_widget']);
Route::get('/builder/example/bootsrap_component', [Example::class, 'bosstrap_comp']);
Route::get('/builder/example/bootsrap_component/load_tab_page', [Example::class, 'load_tab_page']);



Route::group(['middleware' => 'auth'], function (){
    
    Route::get('beranda/index', 'HomeController@index');

    Route::get('employee/get_dataTable','EmployeeController@get_dataTable');
    Route::resource('employee', EmployeeController::class);
    Route::get('ms_user/get_dataTable', 'Ms_userController@get_dataTable');
    Route::resource('ms_user', Ms_userController::class);
    Route::get('ms_item/get_dataTable', 'Ms_itemController@get_dataTable');
    Route::resource('ms_item', Ms_itemController::class);
    Route::get('ms_menu/get_dataTable', 'Ms_menuController@get_dataTable');
    Route::resource('ms_menu', Ms_menuController::class);

    Route::get('ms_item/get_dataTable', 'Ms_itemController@get_dataTable');
    Route::get('ms_item/index2', 'Ms_itemController@index2');
    Route::resource('ms_item', Ms_itemController::class);

    Route::get('ms_classification/get_dataTable', 'Ms_classificationController@get_dataTable');
    Route::get('ms_classification/index2', 'Ms_classificationController@index2');
    Route::resource('ms_classification', Ms_classificationController::class);

    Route::get('ms_user/get_dataTable','Ms_userController@get_dataTable');
    Route::resource('ms_user', Ms_userController::class);

    Route::get('detail_tindakan_medis/data_tindakan', function () {
        return view('detail_tindakan_medis.list_tindakan');
    });
    Route::get('detail_tindakan_medis/download_form', function () {
        return view('detail_tindakan_medis.form_download');
    });
    Route::post('detail_tindakan_medis/kroscek_tindakan', 'Detail_tindakan_medisController@kroscek_tindakan');
    Route::get('detail_tindakan_medis/download_page', 'Detail_tindakan_medisController@get_data_download');
    Route::get('detail_tindakan_medis/get_error/{type?}', 'Detail_tindakan_medisController@get_error');
    Route::post('detail_tindakan_medis/get_data_simrs', 'Detail_tindakan_medisController@get_data_simrs');
    Route::get('detail_tindakan_medis/get_dataTable','Detail_tindakan_medisController@get_dataTable');
    Route::resource('detail_tindakan_medis', Detail_tindakan_medisController::class);

    Route::get('employee_off/get_dataTable','Employee_offController@get_dataTable');
    Route::resource('employee_off', Employee_offController::class);

    Route::get('ms_menu/get_dataTable','Ms_menuController@get_dataTable');
    Route::resource('ms_menu', Ms_menuController::class);

    Route::get('indikator/get_dataTable','IndikatorController@get_dataTable');
    Route::resource('indikator', IndikatorController::class);

    Route::get('detail_indikator/get_dataTable','Detail_indikatorController@get_dataTable');
    Route::resource('detail_indikator', Detail_indikatorController::class);

    Route::get('ms_unit/get_dataTable','Ms_unitController@get_dataTable');
    Route::resource('ms_unit', Ms_unitController::class);

    Route::get('diklat/get_dataTable','DiklatController@get_dataTable');
    Route::resource('diklat', DiklatController::class);

    Route::get('tugas_tambahan/get_dataTable','Tugas_tambahanController@get_dataTable');
    Route::resource('tugas_tambahan', Tugas_tambahanController::class);
});

        Route::get('klasifikasi_jasa/get_dataTable','Klasifikasi_jasaController@get_dataTable');
        Route::resource('klasifikasi_jasa', Klasifikasi_jasaController::class);
        Route::get('komponen_jasa/get_dataTable','Komponen_jasaController@get_dataTable');
        Route::resource('komponen_jasa', Komponen_jasaController::class);

        Route::get('skor_pegawai/error_skor','Skor_pegawaiController@error_skor');
        Route::post('skor_pegawai/save_skor','Skor_pegawaiController@save_skor');
        Route::post('skor_pegawai/generate_skor','Skor_pegawaiController@generate_skor');
        Route::get('skor_pegawai/hasil_skor','Skor_pegawaiController@hasil_skor');
        Route::get('skor_pegawai/get_data','Skor_pegawaiController@get_data');
        Route::get('skor_pegawai/get_dataTable','Skor_pegawaiController@get_dataTable');

        Route::resource('skor_pegawai', Skor_pegawaiController::class);
        Route::get('potongan_statis/get_dataTable','Potongan_statisController@get_dataTable');
        Route::resource('potongan_statis', Potongan_statisController::class);

        Route::get('ms_group/get_hak_akses','ms_groupController@get_hak_akses');
        Route::get('ms_group/get_dataTable','ms_groupController@get_dataTable');
        Route::resource('ms_group', ms_groupController::class);
        Route::get('kategori_potongan/get_dataTable','Kategori_potonganController@get_dataTable');
        Route::resource('kategori_potongan', Kategori_potonganController::class);
        Route::get('kategori_potongan/get_dataTable','Kategori_potonganController@get_dataTable');
        Route::resource('kategori_potongan', Kategori_potonganController::class);
        Route::get('group_refference/get_dataTable','Group_refferenceController@get_dataTable');
        Route::resource('group_refference', Group_refferenceController::class);

        Route::get('ms_reff/data/{id?}', 'Ms_reffController@data');
        Route::get('ms_reff/get_dataTable','Ms_reffController@get_dataTable');
        Route::resource('ms_reff', Ms_reffController::class);

        Route::get('performa_index/data/{performa_id?}','Performa_indexController@data');
        Route::get('performa_index/create/{performa_id?}','Performa_indexController@create');
        Route::get('performa_index/get_dataTable','Performa_indexController@get_dataTable');
        Route::resource('performa_index', Performa_indexController::class);

        
        Route::get('jasa_pelayanan/get_dataTableEmployee','Jasa_pelayananController@get_dataTableEmployee');
        Route::get('jasa_pelayanan/excel/{jaspel_id?}','Jasa_pelayananController@export_excel');
        Route::get('jasa_pelayanan/print/{jaspel_id?}','Jasa_pelayananController@print_pdf');
        Route::get('jasa_pelayanan/list','Jasa_pelayananController@list');
        Route::get('jasa_pelayanan/get_dataTable','Jasa_pelayananController@get_dataTable');
        Route::get('jasa_pelayanan/hasil_hitung_sementara','Jasa_pelayananController@hasil_perhitungan');
        Route::post('jasa_pelayanan/hitung_jasa','Jasa_pelayananController@hitung_jasa');
        Route::resource('jasa_pelayanan', Jasa_pelayananController::class);

        Route::get('proporsi_jasa_individu/get_dataTable','Proporsi_jasa_individuController@get_dataTable');
        Route::post('proporsi_jasa_individu/clear_data','Proporsi_jasa_individuController@clear_data');
        Route::post('proporsi_jasa_individu/insert_right','Proporsi_jasa_individuController@insert_right');
        Route::post('proporsi_jasa_individu/insert_left','Proporsi_jasa_individuController@insert_left');
        Route::post('proporsi_jasa_individu/copy_data','Proporsi_jasa_individuController@copy_data');
        Route::resource('proporsi_jasa_individu', Proporsi_jasa_individuController::class);
        Route::get('repository_download/get_dataTable','Repository_downloadController@get_dataTable');
        Route::resource('repository_download', Repository_downloadController::class);
        Route::get('komponen_jasa_sistem/get_dataTable','Komponen_jasa_sistemController@get_dataTable');
        Route::resource('komponen_jasa_sistem', Komponen_jasa_sistemController::class);

        Route::get("pencairan_jasa_header/data",function(){
            return view("pencairan_jasa_header.data");
        });
        Route::get('pencairan_jasa_header/update_potongan','Pencairan_jasa_headerController@update_potongan');
        Route::get('pencairan_jasa_header/final_pencairan/{id?}','Pencairan_jasa_headerController@final_pencairan');
        Route::get('pencairan_jasa_header/detail/{type?}/{id_kategori?}/{id_jasa?}','Pencairan_jasa_headerController@detail');
        Route::get('pencairan_jasa_header/kroscek/{id?}','Pencairan_jasa_headerController@kroscek');
        Route::get('pencairan_jasa_header/print/{id?}','Pencairan_jasa_headerController@print_pdf');
        Route::get('pencairan_jasa_header/get_dataTable','Pencairan_jasa_headerController@get_dataTable');
        Route::resource('pencairan_jasa_header', Pencairan_jasa_headerController::class);
        
        Route::get('klasifikasi_pajak_penghasilan/get_dataTable','Klasifikasi_pajak_penghasilanController@get_dataTable');
        Route::resource('klasifikasi_pajak_penghasilan', Klasifikasi_pajak_penghasilanController::class);
        Route::get('potongan_jasa_individu/get_dataTable','Potongan_jasa_individuController@get_dataTable');
        Route::resource('potongan_jasa_individu', Potongan_jasa_individuController::class);

        Route::get('users/get_dataTable','UsersController@get_dataTable');
        Route::resource('users', UsersController::class);

        Route::get('laporan','LaporanController@index');
        Route::post('laporan/laporan_pajak','LaporanController@get_lap_pajak');