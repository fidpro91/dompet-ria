<?php

use App\Http\Controllers\Builder\Docs\Example;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\LoginController;
use App\Libraries\Servant;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use App\Models\Diklat;
use App\Traits\WablasTrait;
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
Route::post('login/login_verif', [LoginController::class, 'login_verif']);
Route::get('/', [LoginController::class, 'login'])->name('login');

Route::group(['prefix'=>'mobile','middleware' => ['auth','client']], function(){
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

Route::get('/tes_package', function () {

    Servant::send_wa("POST","",null);
    die;
    $pegawai = \App\Models\Employee::all();
    foreach ($pegawai as $key => $pgw) {
        \App\Models\Diklat::from("diklat as dk")
        ->where("peserta_id",$pgw->emp_id)
        ->join("detail_indikator as di","di.detail_id","=","dk.indikator_skor")
        ->join("indikator as i","i.id","=","di.indikator_id");
    }
    die;
    $kumpulan_data=[];
    //KIRIM WA
    $data['phone'] = '085655448087';
    $data['message'] = 'INI PESAN';
    $data['secret'] = false;
    $data['retry'] = false;
    $data['isGroup'] = false;
    array_push($kumpulan_data, $data);
    WablasTrait::sendText($kumpulan_data);
    die;

    $data = [
        'name' => 'Syahrizal As',
        'body' => 'Testing Kirim Email di Santri Koding'
    ];
   
    Mail::to('ufi.alfi@gmail.com')->send(new SendEmail($data));
   
    dd("Email Berhasil dikirim.");
    die;
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

Route::group(['middleware' => ['auth']], function (){
    Route::post('rekap_jaspel/detail','Rekap_jaspelController@detail');
    Route::get('rekap_jaspel','Rekap_jaspelController@index');

    Route::get('user_profil','UserprofilController@index');
    Route::post('user_profil/update_data','UserprofilController@update_data');
});


Route::get('verifikasi_skor/login',function(){
    $titlePage = "Login Verifikator Skor";
    return view("verifikasi_skor/form_login",compact('titlePage'));
});
Route::get('verifikasi_skor','Verifikasi_skorController@index');
Route::get('verifikasi_skor/get_data/{bulan?}','Verifikasi_skorController@get_data');
Route::post('verifikasi_skor/validasi_otp','Verifikasi_skorController@validasi_otp');
Route::post('verifikasi_skor/save_keluhan','Verifikasi_skorController@save_keluhan');
Route::get('verifikasi_skor/konfirmasi_skor/{id?}','Verifikasi_skorController@konfirmasi_skor');

Route::group(['middleware' => ['auth','admin']], function (){
    
    Route::get('beranda/index', 'HomeController@index');

    Route::get('employee/custom_update','EmployeeController@custom_update');
    Route::post('employee/update_data','EmployeeController@update_data');
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
    Route::get('detail_tindakan_medis/set_mapping_bill/{id?}/{klasifikasi?}','Detail_tindakan_medisController@set_mapping_bill');
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

    Route::get('diklat/set_indikator_skor/{id?}/{skor_id?}','DiklatController@set_indikator_skor');
    Route::get('diklat/view_file/{id?}',function($id){
        $data = Diklat::findOrFail($id);
        return view("diklat.view_file_sertifikat",compact('data'));
    });
    Route::get('diklat/verifikasi_diklat','DiklatController@verifikasi_diklat');
    Route::get('diklat/get_data_diklat','DiklatController@get_data_diklat');
    Route::get('diklat/get_dataTable','DiklatController@get_dataTable');
    Route::post('diklat/update_data','DiklatController@update_data');
    Route::resource('diklat', DiklatController::class);

    Route::get('tugas_tambahan/get_dataTable','Tugas_tambahanController@get_dataTable');
    Route::post('tugas_tambahan/update_data','Tugas_tambahanController@update_data');
    Route::resource('tugas_tambahan', Tugas_tambahanController::class);

    Route::get('klasifikasi_jasa/get_dataTable','Klasifikasi_jasaController@get_dataTable');
    Route::resource('klasifikasi_jasa', Klasifikasi_jasaController::class);
    Route::get('komponen_jasa/get_dataTable','Komponen_jasaController@get_dataTable');
    Route::resource('komponen_jasa', Komponen_jasaController::class);
    
    Route::get('skor_pegawai/set_skor/{type?}','Skor_pegawaiController@set_skor');
    Route::get('skor_pegawai/error_skor','Skor_pegawaiController@error_skor');
    Route::post('skor_pegawai/clear_all_data','Skor_pegawaiController@clear_all_data');
    Route::post('skor_pegawai/send_to_verifikator','Skor_pegawaiController@send_to_verifikator');
    Route::post('skor_pegawai/save_skor','Skor_pegawaiController@save_skor');
    Route::post('skor_pegawai/generate_skor','Skor_pegawaiController@generate_skor');
    Route::get('skor_pegawai/hasil_skor','Skor_pegawaiController@hasil_skor');
    Route::get('skor_pegawai/get_data','Skor_pegawaiController@get_data');
    Route::get('skor_pegawai/get_dataTable','Skor_pegawaiController@get_dataTable');

    Route::resource('skor_pegawai', Skor_pegawaiController::class);
    Route::get('potongan_statis/get_dataTable','Potongan_statisController@get_dataTable');
    Route::resource('potongan_statis', Potongan_statisController::class);

    Route::get('ms_group/get_hak_akses','Ms_groupController@get_hak_akses');
    Route::get('ms_group/get_dataTable','Ms_groupController@get_dataTable');
    Route::resource('ms_group', Ms_groupController::class);
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
    
    Route::get('jasa_pelayanan/simpan_per_proporsi/{jaspel_id?}/{komponen_id?}',"Jasa_pelayananController@simpan_per_proporsi");
    Route::get('jasa_pelayanan/get_dataTableEmployee','Jasa_pelayananController@get_dataTableEmployee');
    Route::get('jasa_pelayanan/excel/{jaspel_id?}','Jasa_pelayananController@export_excel');
    Route::get('jasa_pelayanan/print/{jaspel_id?}','Jasa_pelayananController@print_pdf');
    Route::get('jasa_pelayanan/remove_jaspel/{jaspel_id?}','Jasa_pelayananController@remove_jaspel');
    Route::get('jasa_pelayanan/list','Jasa_pelayananController@list');
    Route::get('jasa_pelayanan/get_dataTable','Jasa_pelayananController@get_dataTable');
    Route::get('jasa_pelayanan/hasil_hitung_sementara','Jasa_pelayananController@hasil_perhitungan');
    Route::post('jasa_pelayanan/hitung_jasa','Jasa_pelayananController@hitung_jasa');
    Route::post('jasa_pelayanan/finish_jaspel','Jasa_pelayananController@finish_jaspel');
    Route::get('jasa_pelayanan/cetak','Jasa_pelayananController@cetak_laporan');
    Route::resource('jasa_pelayanan', Jasa_pelayananController::class);

    Route::get('proporsi_jasa_individu/get_dataTable','Proporsi_jasa_individuController@get_dataTable');
    Route::post('proporsi_jasa_individu/clear_data','Proporsi_jasa_individuController@clear_data');
    Route::post('proporsi_jasa_individu/insert_right','Proporsi_jasa_individuController@insert_right');
    Route::post('proporsi_jasa_individu/insert_left','Proporsi_jasa_individuController@insert_left');
    Route::post('proporsi_jasa_individu/copy_data','Proporsi_jasa_individuController@copy_data');
    Route::resource('proporsi_jasa_individu', Proporsi_jasa_individuController::class);

    Route::post('repository_download/copy_point','Repository_downloadController@copy_point');
    Route::get('repository_download/get_dataTable','Repository_downloadController@get_dataTable');
    Route::get('repository_download/delete_copy/{id?}','Repository_downloadController@delete_copy');
    Route::resource('repository_download', Repository_downloadController::class);
    
    Route::get('komponen_jasa_sistem/get_dataTable','Komponen_jasa_sistemController@get_dataTable');
    Route::resource('komponen_jasa_sistem', Komponen_jasa_sistemController::class);

    Route::get("pencairan_jasa_header/data",function(){
        return view("pencairan_jasa_header.data");
    });
    Route::get('pencairan_jasa_header/statistik/{id?}','Pencairan_jasa_headerController@statistic_report');
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
    Route::get('users/edit_data/{id?}','UsersController@edit_data');
    Route::resource('users', UsersController::class);

    Route::get('laporan','LaporanController@index');
    Route::get('laporan/skor_pegawai','LaporanController@skor_pegawai');
    Route::post('laporan/show_skor_pegawai','LaporanController@get_lap_skor');
    Route::post('laporan/laporan_pajak','LaporanController@get_lap_pajak');
    Route::post('laporan/laporan_potongan','LaporanController@get_lap_potongan');
    Route::get('pencairan_jasa/get_dataTable','Pencairan_jasaController@get_dataTable');
    Route::resource('pencairan_jasa', Pencairan_jasaController::class);
});
Route::get('activity_log/get_dataTable','activity_logController@get_dataTable');
Route::resource('activity_log', activity_logController::class);

Route::get('data_simr/get_dataTable','Data_simrsController@get_dataTable');
Route::resource('data_simr', Data_simrsController::class);
Route::get('data_simr/get_dataTable','Data_simrsController@get_dataTable');
Route::resource('data_simr', Data_simrsController::class);
Route::get('detail_tindakan_medi/get_dataTable','Detail_tindakan_medisController@get_dataTable');
Route::resource('detail_tindakan_medi', Detail_tindakan_medisController::class);

Route::get('potongan_penghasilan/index/{id_cair?}','Potongan_penghasilanController@index');
Route::get('potongan_penghasilan/show/{id?}','Potongan_penghasilanController@show');
Route::get('potongan_penghasilan/data/{id?}','Potongan_penghasilanController@data');
Route::get('potongan_penghasilan/get_dataTable','Potongan_penghasilanController@get_dataTable');
Route::resource('potongan_penghasilan', Potongan_penghasilanController::class);

Route::get('pengajuan_diklat','PengajuandiklatController@index');
Route::post('pengajuan_diklat/send_otp','PengajuandiklatController@send_otp');
Route::post('pengajuan_diklat/find','PengajuandiklatController@find');
Route::post('pengajuan_diklat/validasi_capcha','PengajuandiklatController@validasi_capcha');
Route::post('pengajuan_diklat/store','PengajuandiklatController@store');
Route::get('pengajuan_diklat/form_pengajuan','PengajuandiklatController@form_pengajuan');
Route::get('pengajuan_diklat/finish','PengajuandiklatController@finish');