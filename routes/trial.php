<?php

use App\Libraries\Servant;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Libraries\Qontak;

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
Route::get('cekssl', function () {
   // Generate RSA Key Pair
   $keyPair = openssl_pkey_new([
        'private_key_bits' => 2048,
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
    ]);
    $error = openssl_error_string();
    // Dapatkan kunci privat dan publik dari pasangan kunci
    $success = openssl_pkey_export($keyPair, $privateKey);
    if (!$success) {
        die('Gagal mengekspor kunci privat');
    }
    $publicKeyDetails = openssl_pkey_get_details($keyPair);
    $publicKey = $publicKeyDetails['key'];

    // Tampilkan kunci privat dan publik
    echo "Private Key:\n";
    echo $privateKey . "\n\n";

    echo "Public Key:\n";
    echo $publicKey . "\n";
});

Route::get('slip_remun/{filepdf}', function ($filepdf) {
    // Kunci khusus
    $customKey = '@RSig2024';
    $filepdf = '1-35';
    // Mengenkripsi data dengan kunci khusus
    $encryptedData = Crypt::encryptString($filepdf, $customKey);

    echo "Original Data: $filepdf\n";
    echo "Encrypted Data: $encryptedData\n";

    // Mendekripsi data dengan kunci khusus
    $decryptedData = Crypt::decryptString($filepdf, $customKey);

    echo "Decrypted Data: $decryptedData\n";
    /* $pdfPath = storage_path('app/public/slip_remun/THP_00016_03.01.2024/'.$filepdf);
    return response()->file($pdfPath); */
})->where('filepdf', '.*');

Route::get('/tes_package', function () {
    $customKey = '@RSig2024';
    $link = Crypt::encryptString("1|35",$customKey);
    $link = "http://localhost:88/slip_remun/download/".$link;
    $message = [
        "message"   => "*SLIP REMUNRASI*.\n button \nSilahkan Klik link dibawah ini untuk mengetahui rincian perolehan jasa pelayanan anda. Link ini bersifat privasi dan tidak boleh dishare. Terima Kasih.\n\n\n".$link,
        "number"    => "6285755555091",
        "button"    => ["button 1","button 2","button 3"]
    ];
    $respond=Servant::send_wa("POST",$message);

    print_r($respond);
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

Route::get('/employee', function () {
    $employees = Employee::search('mufid')->get();

    foreach ($employees as $employee) {
        echo $employee->emp_name;
    }
});

Route::get('/kirim-wa', function () {
    $nomor = "+6285755555091";
    $name = "Mufid";
    return Qontak::sendOTP($nomor,$name);
});

Route::get('/prestige/rekap_absen',"Api\PrestigeController@get_rekap_presensi_absen");
Route::get('/prestige/insert_kedisiplinan',"Api\PrestigeController@insert_kedisiplinan");

Route::get('/pencairan_jasa', function () {
    $id=22;
    $ekseKutif = DB::select("
    SELECT 'EKSEKUTIF' AS penjamin, SUM(jm.nominal_terima) AS total
    FROM jp_byname_medis jm
    JOIN jasa_pelayanan jp ON jm.jaspel_id = jp.jaspel_id
    WHERE jp.id_cair = ? AND jm.komponen_id = 9", [$id]);
    $percent = $ekseKutif[0]->total/1000000000*100;
    $percentase[] = [
        "penjamin"          => "EKSEKUTIF",
        "persentase_jasa"   => ($percent),
        "id_cair"           => $id
    ];
    $medisNonEks = DB::select("
    SELECT mr.reff_name AS penjamin, SUM(dm.skor / 10000) AS total_skor
    FROM point_medis dm
    JOIN ms_reff mr ON mr.reff_code = dm.penjamin AND mr.reffcat_id = 5
    JOIN jp_byname_medis jm ON jm.jp_medis_id = dm.jp_medis_id
    JOIN jasa_pelayanan jp ON jm.jaspel_id = jp.jaspel_id
    WHERE jp.id_cair = ? AND jm.komponen_id = 7
    GROUP BY mr.reff_name", [$id]);

    $bagianNonEks=100000000-$ekseKutif[0]->total;
    $totalSkor = array_sum(array_map(function ($value) {
        return $value->total_skor;
    }, $medisNonEks));
    $percentase = array_merge($percentase, array_map(function ($value) use ($totalSkor, $bagianNonEks, $id, $percent) {
        $hitungProporsi = ($value->total_skor/$totalSkor*$bagianNonEks)/$bagianNonEks*(100-$percent);
        return [
            "penjamin"        => $value->penjamin,
            "persentase_jasa" => $hitungProporsi,
            "id_cair"         => $id
        ];
    }, $medisNonEks));
            dd($percentase);
});

use App\Jobs\TesJob;
Route::get('/tes_skor', function () {
    TesJob::dispatch();
});