<?php
namespace App\Libraries;

use App\Models\Log_messager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
 
class Servant
{
    public static $apiKeyWa = '8OUgqrwhsyUlCbz4GEPTVyBDTpHUNb08';
    public static $phoneWa  = '6285655448087';
    
    public static function connect_simrs($method,$url,$data = array()){
        $ch = curl_init(); 
        $base_url = "http://localhost:88/ehos/api/api_internal/";
        $url = $base_url.$url;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array("Content-Type: application/json"));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $result = curl_exec($ch);
        if(curl_errno($ch)){
            echo 'Request Error:' . curl_error($ch);
        }
        curl_close($ch);
        return ($result);
    }

    public static function get_menu($id=0)
    {
        $datam =    DB::table('ms_menu as m')->where([
                        "menu_parent_id"	=> $id,
                        "menu_status"	    => 't',
                        "ga.group_id"       => Auth::user()->group_id
                    ])
                    ->join("group_access as ga","ga.menu_id","=","m.menu_id");
        $menux='';
        foreach ($datam->get() as $key => $value) {
            if ( DB::table('ms_menu')->where(["menu_parent_id"	=> $value->menu_id])->count() > 0) {
                $menux .= "<li class=\"has-submenu\"><a href=\"#\">
                                <i class=\"".(!empty($value->menu_icon)?$value->menu_icon:'fa fa-circle-o')."\"></i> ".strtoupper($value->menu_name)." <i class=\"arrow-down\"></i>
                                </a>
                                <ul class=\"submenu\">";
                $menux .= self::get_menu($value->menu_id);
                $menux .= "</ul></li>";
            }else{
                $menux .= "<li><a href=\"".URL($value->menu_url)."\">
                        <i class=\"".(!empty($value->menu_icon)?$value->menu_icon:'')."\"></i> ".strtoupper($value->menu_name)."
                        </a></li>";
            }
        }
        return $menux;
    }

    public static function generate_code_transaksi($data){
		$query = DB::table($data['table'])->selectRaw("LPAD((max(COALESCE(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(".$data['column'].",'".$data['delimiterFirst']."',".$data['limit']."),'".$data['delimiterLast']."','".$data['number']."') AS UNSIGNED),0))+1),5,'0') AS nomax")->get()->first();
		if (empty($query->nomax)) {
            $query->nomax = 1;
        }
        return str_replace('NOMOR', $query->nomax, $data['text']);
	}

    public static function send_wa($method,$param){
        $ch = curl_init(); 
        $url = "https://wagw.ariefsetyan.my.id/send-message";
        // $url = "http://192.168.1.24:8000/send-message";
        $data = [
            "api_key"   => self::$apiKeyWa,
            "sender"    => self::$phoneWa
        ];
        $param = array_merge($data,$param);
        $param = json_encode($param);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array("Content-Type: application/json"));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $result = curl_exec($ch);
        if(curl_errno($ch)){
            echo 'Request Error:' . curl_error($ch);
        }
        curl_close($ch);
        $resp = [
            "param"     => $param,
            "response"  => json_decode($result,true)
        ];
        return $resp;
    }
}