<?php
namespace App\Libraries;

use App\Models\Log_messager;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Qontak
{
    private function get_token(){
        $url = env('QONTAK_URL').'oauth/token';
        $client = new Client();
        $auth = [
            "username"          => env("QONTAK_USERNAME"),
            "password"          => env("QONTAK_PASSWORD"),
            "grant_type"        => "password",
            "client_id"         => env("QONTAK_CLIENT_ID"),
            "client_secret"     => env("QONTAK_CLIENT_SECRET")
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

    public static function sendOTP($number,$name,$code){
        $client = new Client();

        $number = self::validNumber($number);
        if (!$number) {
            return ([
                "code"      => 202,
                "message"   => "Nomor WA tidak valid",
                "number"    => $number
            ]);
        }
        try {
            $token = self::get_token();
            $token = $token->access_token;
            $otpCode = $code;
            $url = env("QONTAK_URL")."api/open/v1/broadcasts/whatsapp/direct";
            $data = [
                "to_number" => "$number",
                "to_name" => "$name",
                "message_template_id" => "1e011475-6318-42ff-8fb1-0797f61ccec6",
                "channel_integration_id" => "141d140b-813e-4df5-99a3-557e0322d831",
                "language" => [
                    "code" => "id"
                ],
                "parameters" => [
                    "body" => [
                        [
                            "key" => "1",
                            "value" => "$otpCode",
                            "value_text" => "$otpCode"
                        ]
                    ],
                    "buttons" => [
                        [
                            "index" => "0",
                            "type" => "url",
                            "value" => "$otpCode"
                        ]
                    ]
                ]
            ];
            Log_messager::create([
                'param'             => json_encode($data),
                'kode_otp'          => $otpCode,
                'phone_number'      => $number,
                'message_status'    => 2,
                'message_type'      => 1,
            ]);
            $response = $client->request('POST', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $data
            ]);
            $body = $response->getBody();
            $content = json_decode($body, true);
            return $content;
        } catch (RequestException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function validNumber($phoneNumber) {
        $phoneNumber = trim($phoneNumber);
        if (str_starts_with($phoneNumber, '+62')) {
            return $phoneNumber;
        }
        if (str_starts_with($phoneNumber, '62')) {
            return '+' . $phoneNumber;
        }
        if (str_starts_with($phoneNumber, '0')) {
            return '+62' . substr($phoneNumber, 1);
        }
    }

    public static function sendInfoSkor($number,$name,$penerima){
        $client = new Client();

        $number = self::validNumber($number);
        if (!$number) {
            return ([
                "status"    => "failed",
                "message"   => "Nomor WA tidak valid",
                "number"    => $number
            ]);
        }
        try {
            $token = self::get_token();
            $token = $token->access_token;
            $url = env("QONTAK_URL")."api/open/v1/broadcasts/whatsapp/direct";
            $data = [
                "to_number" => "$number",
                "to_name" => "$name",
                "message_template_id" => "705fe9df-0f28-4842-9d26-c30ab4934f4d",
                "channel_integration_id" => "141d140b-813e-4df5-99a3-557e0322d831",
                "language" => [
                    "code" => "id"
                ],
                "parameters" => [
                    "body" => [
                        [
                            "key" => "1",
                            "value" => "penerima",
                            "value_text" => "".$penerima["penerima"].""
                        ],
                        [
                            "key" => "2",
                            "value" => "unitkerja",
                            "value_text" => "".$penerima["unit"].""
                        ]
                    ]
                ]
            ];
            Log_messager::create([
                'param'             => json_encode($data),
                'phone_number'      => $number,
                'message_status'    => 2,
                'message_type'      => 1,
            ]);
            $response = $client->request('POST', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $data
            ]);
            $body = $response->getBody();
            $content = json_decode($body, true);
            return $content;
        } catch (RequestException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
}