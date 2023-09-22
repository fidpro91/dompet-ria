<?php
$getHeader = new ArrayIterator($skorPegawai);
$header = json_decode($getHeader->current()->detail);
?>
<style>
    .table {
        border-collapse: collapse;
        width: 100%;
        border: 1px solid;
    }
    th {
        border: 1px solid black;
        font-weight: 100pt;
        font-size: 12pt;
        background-color: #E4E2E8;
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
    .center {
        text-align: center;
    }
</style>
<H4>LAPORAN SKOR INDIVIDU PEGAWAI</H4>
<table>
    <tr>
        <td>BULAN SKOR</td>
        <td>:</td>
        <td><?=$bulanSkor?></td>
    </tr>
    <tr>
        <td>UNIT KERJA</td>
        <td>:</td>
        <td><?=$unitKerja?></td>
    </tr>
</table>
<table class="table">
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">NIP</th>
                <th rowspan="2">NAMA</th>
                <th rowspan="2">UNIT KERJA</th>
                <?php
                    foreach ($header as $key => $value) {
                        echo "<th colspan=\"2\">".strtoupper($value->kode)."</th>";
                    }
                ?>
                <th rowspan="2">TOTAL SKOR</th>
            </tr>
            <tr>
                <?php
                    foreach ($header as $key => $value) {
                        echo "<th>SKOR</th><th>DETAIL</th>";
                    }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
                $row='';$totalSkor=0;
                foreach ($skorPegawai as $key => $value) {
                    $row .= '
                    <tr>
                        <td>'.($key+1).'</td>
                        <td>'.$value->emp_no.'</td>
                        <td>'.$value->emp_name.'</td>
                        <td>'.$value->unit_name.'</td>';
                    foreach ($header as $x => $res) {
                        $detail = json_decode($value->detail,true);
                        $kode=$res->kode;
                        $detail = array_filter($detail,function($var) use($kode){
                            return $var["kode"] == $kode;
                        });
                        $detail = array_values($detail);
                        $row .= '
                            <td class="center">'.($detail[0]['skor']??0).'</td>
                            <td>'.($detail[0]['keterangan']??"").'</td>
                        ';
                    }
                    $row .= '<td>'.$value->total_skor.'</td>';
                    $row .= '</tr>';
                    $totalSkor += $value->total_skor;
                }
                echo $row;
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td colspan="19">TOTAL SKOR</td>
                <td><?=$totalSkor?></td>
            </tr>
        </tfoot>
    </table>