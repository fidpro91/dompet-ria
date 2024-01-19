<style type="text/css">
@media print {
  footer {page-break-after: always;}
}
	.table {
    border-collapse: collapse;
    width: 100%;
}

th {
    border-top: 1px solid black;
    border-bottom: 1px solid black;
    font-weight: 100pt;
}
.table td {
  padding: 5px;
}
tfoot tr:first-child td {
    border-top: 1px solid black;
    border-bottom: 1px solid black;
}

.body-tabel td {
  border: 0px !important;
  border-style: none !important;
  border-color: #fff !important;
}
.header th,td {
  padding:0px; margin:0px;
}

hr {
  line-height: 0px;
}

h4,h3 {
  font-weight: 150pt;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  font-size: small;
}
.text-center {
	text-align: center;
}
.angka {
    text-align: right;
}
.subtitle {
    text-align: center;
    background-color: #D1CFCF;
    font-weight: bold;
}
.hilang {
    visibility: hidden;
}
</style>
<h1 align="center">
    <b>LAPORAN REKAPITULASI  <?=$data['header'][0]->keterangan?><br>
    RSUD IBNU SINA KABUPATEN GRESIK</b><br>
    <small>Bulan <?=get_namaBulan($data['header'][0]->jaspel_bulan)?> Tahun <?=$data['header'][0]->jaspel_tahun?></small>
</h1>
<P></P>
<P></P>
	<table class="table" border="1">
        <thead>
            <tr>
                <td>TANGGAL HITUNG : </td>
                <td colspan="4"><?=date('d-m-Y',strtotime($data['header'][0]->tanggal_jaspel))?></td>
            </tr>
            <tr>
                <td>NOMOR : </td>
                <td colspan="4"><?=$data['header'][0]->no_jasa?></td>
            </tr>
            <tr>
                <td colspan="2">TOTAL PENDAPATAN :</td>
                <td colspan="2"></td>
                <td class="angka"><?=convert_currency2($data['header'][0]->nominal_pendapatan)?></td>
            </tr>
            <tr>
                <td colspan="2">PROPORSI JASA :</td>
                <td colspan="2" class="angka"><?=$data['header'][0]->percentase_jaspel?>%</td>
                <td class="angka"><?=convert_currency2($data['header'][0]->nominal_jaspel)?></td>
            </tr>
            <tr>
                <th>NO</th>
                <th>PROPORSI JASA</th>
                <th>PERCENTASE(%)</th>
                <th>NOMINAL(Rp)</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
                $row="";
                foreach ($data['header'] as $key => $value) {
                    $row .= "
                        <tr>
                            <td>".($key+1)."</td>
                            <td>$value->komponen_nama</td>
                            <td>$value->percentase</td>
                            <td>".convert_currency2($value->nominal)."</td>
                            <td></td>
                        </tr>
                    ";
                }
                echo $row;
            ?>
        </tbody>
    </table>
    <p></p>
    <p></p>
    <p></p>
    <h2 style="text-align: center; font-weight:bold">JASA PELAYANAN BY NAME</h2>
    <table class="table" border="1">
        <?php
            $row="";
            foreach ($data['detail'] as $key => $value) {
                $detail = json_decode($value->detail);
                usort($detail, function ($a, $b) {
                    $compareUnit = strcmp($a->unit, $b->unit);
                    if ($compareUnit === 0) {
                        // Jika unit sama, sortir berdasarkan nama
                        return strcmp($a->nama, $b->nama);
                    }
                    return $compareUnit;
                });
                $row .= "
                    <tr>
                        <th colspan='5' style='text-align:center'>$value->nama_komponen</th>
                    </tr>
                    <tr>
                        <th>NO</th>
                        <th>NIP</th>
                        <th>NAMA</th>
                        <th>UNIT KERJA</th>
                        <th>SKOR</th>
                        <th>NOMINAL</th>
                    </tr>
                ";
                $totalRow=0;
                foreach ($detail as $x => $v) {
                    $totalRow += $v->nominal;
                    $row .= "
                        <tr>
                            <td>".($x+1)."</td>
                            <td>$v->nip</td>
                            <td>$v->nama</td>
                            <td>$v->unit</td>
                            <td>$v->skor</td>
                            <td>".convert_currency2($v->nominal)."</td>
                        </tr>";
                }
                $row .= "
                <tr>
                    <th></th>
                    <th colspan=\"4\">TOTAL</th>
                    <th>".convert_currency2($totalRow)."</th>
                </tr>";
            }
            echo $row;
        ?>
    </table>
<pagebreak />