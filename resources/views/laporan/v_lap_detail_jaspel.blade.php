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
        background-color: #f8f8f8;
    }
    .table th, .table td {
        padding: 8px;
        text-align: left;
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
    .row-data {
        border: 1px solid #ddd;
        margin: 20px 0;
    }
    .row-header {
        background-color: #f1f1f1;
        padding: 10px;
        font-weight: bold;
        overflow: hidden;
        margin-bottom: 10px;
    }
    .row-body {
        padding: 10px;
        padding-left: 30px;
        overflow: hidden;
    }
    .subtotal-row {
        font-weight: bold;
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

<h3 style="margin-top: 30px; margin-bottom:30px">DETAIL SKOR INDIVIDU PEGAWAI</h3>

{!!
    Bootstrap::tableData($skorPegawai,["class" => "table"],[
        'NO' => [
            'data'  => 'number'
        ],
        'BULAN SKOR' => [
            'data'  => 'bulan'
        ],
        'SKOR INDIVIDU' => [
            'data'  => 'skor'
        ],
        'NILAI BRUTTO (Rp)' => [
            'data'  => 'nilai_brutto',
            'custom'    => function($a){
                return convert_currency2($a['nilai_brutto']);
            }
        ],
        'DETAIL SKORING' => [
            'data'  => 'detail'
        ]
    ])
!!}
@if(Session::get('sesLogin')->is_medis == 't' || Session::get('sesLogin')->group_type == 1)
<h3 style="margin-top: 30px; margin-bottom:30px">DETAIL PEROLEHAN POINT PELAYANAN MEDIS</h3>
<?php
    $sheet = "";
    foreach ($jasa_by_penjamin as $value) {
        $rinci1 = json_decode($value->details);
        $sheet2 = "";
        foreach ($rinci1 as $key => $rs) {
            $sheet3 = "";
            $totalPoints = $subTotalBrutto = 0;
            foreach ($rs->uraian_tindakan as $key1 => $row) {
                $nilaiBrutto = (function () use($value, $row) {
                    $nilai = 0;
                    if (strpos($value->keterangan, "EKSEKUTIF") !== false) {
                        $nilai = ($row->point*10000);
                    } else {
                        $nilai = ($row->point / $value->total_point) * $value->nominal;
                    }
                    return $nilai;
                })();
                $totalPoints += $row->point;
                $subTotalBrutto += $nilaiBrutto;
                $sheet3 .= "<tr>
                    <td>".($key1 + 1)."</td>
                    <td>$row->kodedata</td>
                    <td>$row->tindakan</td>
                    <td>$row->tarif</td>
                    <td>$row->percentase</td>
                    <td>$row->point</td>
                    <td>".convert_currency2($nilaiBrutto)."</td>
                </tr>";
            }

            $sheet3 .= "<tr class=\"subtotal-row\">
                    <td></td>
                    <td colspan=\"4\">SUB TOTAL</td>
                    <td>$totalPoints</td>
                    <td>".convert_currency2($subTotalBrutto)."</td>
                </tr>";

            $sheet2 .= "
            <div class=\"row-data\">
                <div class=\"row-header\">
                    ".($key + 1).". $rs->klasifikasi_jasa
                </div>
                <div class=\"row-body\">
                    <table class=\"table\">
                        <tr>
                            <th>NO</th>
                            <th>KODEDATA</th>
                            <th>NAMA TINDAKAN</th>
                            <th>TARIF</th>
                            <th>PERCENTASE JASA</th>
                            <th>POINT</th>
                            <th>NILAI BRUTTO</th>
                        </tr>
                        $sheet3
                    </table>
                </div>
            </div>
            ";
        }
        echo "<div class=\"row-data\">
            <div class=\"row-header\">
                <div style=\"float: left;\">
                    <p>KETERANGAN BULAN PELAYANAN DAN PENJAMIN : </p>
                    <p>$value->keterangan</p>
                    <p>TOTAL NILAI BRUTTO (Rp) : ".convert_currency2($value->nominal)." </p>
                </div>
                <div style=\"float: right;\">
                    <p>TOTAL POINT PELAYANAN : </p>
                    <p>$value->total_point </p>
                </div>
            </div>
            <div class=\"row-body\">
                $sheet2
            </div>
        </div>";
    }
?>
@endif