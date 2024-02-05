<style type="text/css">
    @media print {
        footer {
            page-break-after: always;
        }
    }

    .table {
        border-collapse: collapse;
        width: 100%;
        table-layout: fixed;
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

    .header th,
    td {
        padding: 0px;
        margin: 0px;
    }

    hr {
        line-height: 0px;
    }

    h4,
    h3 {
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
    <b>LAPORAN PENCAIRAN JASA PELAYANAN<br>
        RSUD IBNU SINA KABUPATEN GRESIK</b>
</h1>
<P></P>
<P></P>
<table class="table" border="1">
    <thead>
        <tr>
            <td>TANGGAL PENCAIRAN : </td>
            <td><?= date('d-m-Y', strtotime($data['header']->tanggal_cair)) ?></td>
        </tr>
        <tr>
            <td>NOMOR : </td>
            <td><?= $data['header']->no_pencairan ?></td>
        </tr>
        <tr>
            <td>TOTAL JASA PELAYANAN :</td>
            <td><?= convert_currency2($data['header']->total_nominal) ?></td>
        </tr>
    </thead>
</table>
<p></p>
<p></p>
<p></p>
<h2 style="text-align: center; font-weight:bold">PERHITUNGAN PAJAK & POTONGAN JASA</h2>
<table class="table" border="1" style="width: 100%;">
    <tr>
        <th style="width: 2%;">NO</th>
        <th style="width: 5%;">NO. REK</th>
        <th style="width: 15%;">NAMA</th>
        <th style="width: 10%;">GOLONGAN</th>
        <th style="width: 5%;">BRUTTO</th>
        <?php
            foreach ($data['potongan'] as $key => $value) {
                echo "<th>$value->nama_kategori</th>";
            }
        ?>
        <th>NETTO</th>
    </tr>
    <?php
    $row = "";
    $totalBrutto=$totalNetto=0;
    $totalPotongan=[];
    foreach ($data['detail'] as $key => $value) {
        $detail = json_decode($value->detail,true);
        $totalBrutto += $value->total_brutto;
        $totalRow = 0;
        $row .= "
        <tr>
            <td>" . ($key + 1) . "</td>
            <td>$value->nomor_rekening</td>
            <td>$value->emp_name</td>
            <td>$value->golongan</td>
            <td>" . convert_currency2($value->total_brutto) . "</td>";
        foreach ($data['potongan'] as $x => $v) {
            $kategori = $v->kategori_potongan_id;
            $potongan = array_filter($detail, function ($var) use($kategori){
				return ($var['kategori_id'] == $kategori);
			});
            $potongan = array_values($potongan);
            $potonganValue=0;
            if ($potongan) {
                $potonganValue = $potongan[0]['potongan'];
            }
            $totalPotongan[$key][$v->kategori_potongan_id] = $potonganValue;
            $totalRow += $potonganValue;
            $row .= "
            <td>
                ".convert_currency2($potonganValue)."
            </td>";
        }
        $netto = $value->total_brutto - $totalRow;
        $totalNetto += $netto;
        $row .= "<td>".convert_currency2($netto)."</td></tr>";
    }
    echo $row;
    ?>
    <tr>
        <td></td>
        <td colspan="3">TOTAL</td>
        <td><?=convert_currency2($totalBrutto)?></td>
        <?php
            $row = "";
            foreach ($data['potongan'] as $x => $v) {
                $row .= "<td>".convert_currency2(array_sum(array_column($totalPotongan,$v->kategori_potongan_id)))."</td>";
            }
            echo $row;
        ?>
        <td><?=convert_currency2($totalNetto)?></td>
    </tr>
</table>
<pagebreak />