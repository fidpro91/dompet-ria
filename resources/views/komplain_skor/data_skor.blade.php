<?php

use App\Models\Detail_skor_pegawai;
use App\Models\Indikator;
use fidpro\builder\Bootstrap;

$info = '<table class="table">';
foreach ($data as $key => $value) {
    $info .= '
    <tr>
        <td>'.$key.'</td>
        <td>:</td>
        <td>'.$value.'</td>
    </tr>';
}
$info .= '</table>';
?>
{!! Form::hidden('id_skor','', array('id' => 'id_skor')) !!}
<?=
    Bootstrap::tabs([
        "tabs"  => function()use($info){
            $data = Detail_skor_pegawai::select('kode_skor')
                    ->distinct()
                    ->get();
            $firstTab["Informasi Pegawai"] = [
                "href"      => "info",
                "content"   => function() use($info){
                    return $info;
                }
            ];
            foreach ($data as $key => $value) {
                $nameTab = strtoupper($value->kode_skor);
                $tabs[$nameTab] = [
                    "href"      => "link_".$value->kode_skor,
                    "url"       => "detail_skor_pegawai/data/".$value->kode_skor
                ];
            }
            $tabs = array_merge($firstTab,$tabs);
            return $tabs;
        }
    ]);
?>