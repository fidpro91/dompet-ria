<?php
use fidpro\builder\Bootstrap;
?>
<style>
    .table {
        border-collapse: collapse;
        width: 100%;
        border: 1px solid;
    }
    .table th {
        border: 1px solid black;
        font-weight: 100pt;
    }
    .table td {
        padding: 5px;
        font-size: 10pt;
        border: 1px solid;
    }
    .body-tabel td {
        border: 1px !important;
        border-color: #fff !important;
    }
    .header th,td {
        padding:0px; margin:0px;
    }
    .text-left {
        text-align: left;
    }
    .text-right {
        text-align: right;
    }
    .table-long {
        page-break-inside: avoid !important;
        padding-left: 30px !important;
    }
</style>
<table width="100%">
    <tr>
        <th colspan="3">
            <table width="100%" style="margin-bottom: 20px;">
                <tr>
                    <th width="18%">
                        <img src="{{public_path('assets/images/logo.png')}}" alt="" style="width: 70%;">
                    </th>
                    <th style="text-align: left; padding:0px">
                        <b style="font-size: 14pt; margin:0px">RINCIAN PERHITUNGAN JASA PELAYANAN</b><br>
                        <span style="font-size: 16pt; margin:0px">RUMAH SAKIT UMUM DAERAH IBNU SINA KABUPATEN GRESIK</span> <br>
                        <i style="font-size: 12pt; margin:0px">Jl. DR. Wahidin Sudiro Husodo No.243B Kabupaten Gresik Jawa Timur (61124)</i>
                    </th>
                </tr>
            </table>
        </th>
    </tr>
    <tr>
        <th width="15%" class="text-left">NIP</th>
        <th>:</th>
        <th class="text-left">{{$profil->emp_no}}</th>
    </tr>
    <tr>
        <th class="text-left">NAMA</th>
        <th>:</th>
        <th class="text-left">{{$profil->emp_name}}</th>
    </tr>
    <tr>
        <th class="text-left">TANGGAL PEMBAGIAN/KETERANGAN</th>
        <th>:</th>
        <th class="text-left">{{date_indo2($profil->tanggal_cair)."/".$profil->keterangan}}</th>
    </tr>
</table>
<table class="table">
    <tr>
        <th class="text-left">JASA BRUTTO :</th>
        <th class="text-left">POTONGAN JASA PELAYANAN :</th>
    </tr>
    <tr>
        <td>
            <table class="table">
                <?php
                $totalBrutto = 0;
                foreach ($jasaBrutto as $key => $value) {
                    echo "
                        <tr>
                            <td>$value->nama_komponen</td>
                            <td class=\"text-right\">" . convert_currency2($value->total_brutto) . "</td>
                        </tr>";
                    $totalBrutto += $value->total_brutto;
                }
                ?>
                <tr>
                    <th class="text-left">TOTAL BRUTTO :</th>
                    <th class="text-right"><?= convert_currency2($totalBrutto) ?></th>
                </tr>
            </table>
        </td>
        <td>
            <table class="table">
                <?php
                $totalPotongan = 0;
                foreach ($potonganJasa as $key => $value) {
                    echo "
                        <tr>
                            <td>$value->potongan_nama</td>
                            <td class=\"text-right\">" . convert_currency2($value->potongan_value) . "</td>
                        </tr>";
                    $totalPotongan += $value->potongan_value;
                }
                ?>
                <tr>
                    <th class="text-left">TOTAL POTONGAN :</th>
                    <th class="text-right"><?= convert_currency2($totalPotongan) ?></th>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <th class="text-left">PENDAPATAN BERSIH :</th>
        <th class="text-right"><?= convert_currency2($totalBrutto - $totalPotongan) ?></th>
    </tr>
</table>
<p></p>
<h2>DETAIL SKOR INDIVIDU PEGAWAI</h2>
{!!
    Bootstrap::tableData($skorPegawai,["class" => "table"])
!!}
@if(Session::get('sesLogin')->is_medis == 't' || Session::get('sesLogin')->group_type == 1)
<h2>JASA PELAYANAN BY PENJAMIN (BRUTTO)</h2>
<?php
$jasa_by_penjamin = array_map('get_object_vars', $jasa_by_penjamin);
?>
{!!
    Bootstrap::tableData($jasa_by_penjamin,["class" => "table"],[
        'NO' => [
            'data'  => 'number'
        ],
        'PELAYANAN' => [
            'data'  => 'pelayanan'
        ],
        'NAMA PENJAMIN' => [
            'data'  => 'nama_penjamin'
        ],
        'SKOR JASA'  => [
            'data'      => 'skor_jasa'
        ],
        'JASA TUNAI'  => [
            'data'      => 'jasa_tunai',
            'custom'    => function($a){
                return convert_currency2($a['jasa_tunai']);
            }
        ],
    ])
!!}
<h2>DETAIL POINT PELAYANAN EKSEKUTIF</h2>
<?php
    $pelayanan = json_decode(json_encode($pelayanan),true);
    $eksekutif = array_values(array_filter($pelayanan, function ($var){
        return ($var['komponen_id'] == 9);
    }));
    $nonEksekutif = array_values(array_filter($pelayanan, function ($var){
        return ($var['komponen_id'] == 7);
    }));
?>
@foreach ($eksekutif as $key => $value)
@php $detail = json_decode($value['detail'], true); @endphp
<div>
    <h3>{{($key+1)}}. {{$value['klasifikasi_jasa']}}</h3>
    {!!
        Bootstrap::tableData($detail,["class" => "table table-long"],[
            'NO' => [
                'data'  => 'number'
            ],
            'KODE DATA' => [
                'data'  => 'id_kunjungan'
            ],
            'TINDAKAN' => [
                'data'  => 'tindakan'
            ],
            'TARIF' => [
                'data'  => 'tarif',
                'custom'    => function($a){
                    return convert_currency2($a['tarif']);
                }
            ],
            'PERCENTASE JASA' => [
                'data'  => 'percentase'
            ],
            'SKOR'  => [
                'data'      => 'skor'
            ],
        ])
    !!}
</div>
@endforeach
<!-- <table class="table table-long">
    <thead>
        <tr>
            <th>NO</th>
            <th width="70%">KLASIFIKASI</th>
            <th>TOTAL SKOR</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $row="";
            foreach ($eksekutif as $key => $value) {
                $detail = json_decode($value['detail'],true);
                $row .= "
                <tr>
                    <th>".($key+1)."</th>
                    <th class=\"text-left\">".$value['klasifikasi_jasa']."</th>
                    <th class=\"text-right\">".$value['total_skor']."</th>
                </tr>
                ";
                $row .= "
                <tr>
                    <th></th>
                    <th colspan=\"2\">
                    ".Bootstrap::tableData($detail,["class" => "table table-long"],[
                        'NO' => [
                            'data'  => 'number'
                        ],
                        'ID KUNJUNGAN' => [
                            'data'  => 'id_kunjungan'
                        ],
                        'TINDAKAN' => [
                            'data'  => 'tindakan'
                        ],
                        'SKOR'  => [
                            'data'      => 'skor'
                        ],
                    ])."
                    </th>
                </tr>";
            }
            echo $row;
        ?>
    </tbody>
</table> -->
<h3>DETAIL POINT PELAYANAN NON EKSEKUTIF</h3>
@foreach ($nonEksekutif as $key => $value)
@php $detail = json_decode($value['detail'], true); @endphp
<div>
    <h3>{{($key+1)}}. {{$value['klasifikasi_jasa']}}</h3>
    {!!
        Bootstrap::tableData($detail,["class" => "table table-long"],[
            'NO' => [
                'data'  => 'number'
            ],
            'KODE DATA' => [
                'data'  => 'id_kunjungan'
            ],
            'TINDAKAN' => [
                'data'  => 'tindakan'
            ],
            'TARIF' => [
                'data'  => 'tarif',
                'custom'    => function($a){
                    return convert_currency2($a['tarif']);
                }
            ],
            'PERCENTASE JASA' => [
                'data'  => 'percentase'
            ],
            'SKOR'  => [
                'data'      => 'skor'
            ],
        ])
    !!}
</div>
@endforeach
<!-- <table class="table table-long">
    <thead>
        <tr>
            <th>NO</th>
            <th width="70%">KLASIFIKASI</th>
            <th>TOTAL SKOR</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $row="";
            foreach ($nonEksekutif as $key => $value) {
                $detail = json_decode($value['detail'],true);
                $row .= "
                <tr>
                    <th>".($key+1)."</th>
                    <th class=\"text-left\">".$value['klasifikasi_jasa']."</th>
                    <th class=\"text-right\">".$value['total_skor']."</th>
                </tr>
                ";
                $row .= "
                <tr>
                    <th></th>
                    <th colspan=\"2\">
                    ".Bootstrap::tableData($detail,["class" => "table table-long"],[
                        'NO' => [
                            'data'  => 'number'
                        ],
                        'ID KUNJUNGAN' => [
                            'data'  => 'id_kunjungan'
                        ],
                        'TINDAKAN' => [
                            'data'  => 'tindakan'
                        ],
                        'SKOR'  => [
                            'data'      => 'skor',
                            'custom'    => function($a){
                                return ($a['skor']/10000);
                            }
                        ],
                    ])."
                    </th>
                </tr>";
            }
            echo $row;
        ?>
    </tbody>
</table> -->
@endif