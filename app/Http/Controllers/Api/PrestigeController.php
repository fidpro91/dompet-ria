<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Log_messager;
use App\Models\Table_rekap_absen;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class PrestigeController extends Controller
{
    private static $token = null;

    public function get_token(){
        $url = env('PRESTIGE_URL').'oauth/token';
        $client = new Client();
        $auth = [
            "username"          => env("PRESTIGE_USERNAME"),
            "password"          => env("PRESTIGE_PASSWORD"),
            "grant_type"        => "password",
            "client_id"         => env("PRESTIGE_CLIENT_ID"),
            "client_secret"     => env("PRESTIGE_CLIENT_SECRET")
        ];

        $response = $client->request('POST', $url, [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'json' => $auth
        ]);
        $body = $response->getBody();
        $content = json_decode($body);
        return $content;
    }

    private static function get_valid_token()
    {
        if (self::$token && (time() - self::$token->created_at < self::$token->expires_in)) {
            return self::$token->access_token;
        }
        self::$token = self::get_token();
        self::$token->created_at = time();
        return self::$token->access_token;
    }

    public function get_rekap_presensi_absen(){
        $url = env('PRESTIGE_URL').'api/list_rekap_absen';
        $client = new Client();
        $token = self::get_valid_token();
        $auth = [
            "tahun"             => "2024",
            "uker"              => "011",
            "satker"            => "042",
            "bulan"             => "09",
            "nip"               => ""
        ];

        $response = $client->request('POST', $url, [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
            'json' => $auth
        ]);
        $body = $response->getBody();

        //insert into table rekap absensi
        $content = json_decode($body);
        foreach ($content as $key => $value) {
            $data = [
                'nip'                   => $value->nip,
                'bulan_update'          => $value->bulan,
                'nama_pegawai'          => $value->nama,
                'tahun_update'          => $value->tahun,
                'persentase_kehadiran'  => $value->index_disiplin,
            ];
            Table_rekap_absen::create($data);
        }
        return $content;
    }

    public function get_absensi_pegawai(){
        $url = env('PRESTIGE_URL').'api/list_absensi_bulan_nip';
        $client = new Client();
        $token = self::get_valid_token();
        $auth = [
            "tahun"             => "2024",
            "bulan"             => "08",
            "nip"               => "43776514"
        ];

        $response = $client->request('POST', $url, [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
            'json' => $auth
        ]);
        $body = $response->getBody();
        $content = json_decode($body);
        return $content;
    }

    public function get_ijin_pegawai(){
        $url = env('PRESTIGE_URL').'api/list_ijin_tahun_nip';
        $client = new Client();
        $token = self::get_valid_token();
        $auth = [
            "tahun"             => "2024",
            "bulan"             => "08",
            "jenis"             => "all",
            "uker"              => "011",
            "satker"            => "042",
            "nip"               => "43776514"
        ];

        $response = $client->request('POST', $url, [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
            'json' => $auth
        ]);
        $body = $response->getBody();
        $content = json_decode($body);
        return $content;
    }

}