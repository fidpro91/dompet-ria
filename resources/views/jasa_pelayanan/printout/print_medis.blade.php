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
<?php
$getHeader = new ArrayIterator($data);
$header = json_decode($getHeader->current()->detail);
?>
<h1 align="center">
</h1>
    <h2 style="text-align: center; font-weight:bold">JASA PELAYANAN MEDIS</h2>
    <table class="table" border="1">
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">NIP</th>
                <th rowspan="2">NAMA</th>
                <?php
                    $child="";
                    foreach ($header as $key => $value) {
                        $child .= "<th>SKOR</th>
                                   <th>NOMINAL</th>";
                        echo "<th colspan=\"2\">$value->komponen</th>";
                    }
                ?>
                <th rowspan="2">TOTAL</th>
            </tr>
            <tr>
                <?=$child?>
            </tr>
        </thead>
        <tbody>
            <?php
                $row="";$arrRow=[];$totalAll=0;
                foreach ($data as $key => $value) {
                    $row .= "
                        <tr>
                            <td>".($key+1)."</td>
                            <td>$value->emp_no</td>
                            <td>$value->emp_name</td>
                    ";
                    $totalRow=0;
                    foreach ($header as $x => $v) {
                        $detail = json_decode($value->detail,true);
                        $id = $v->id;
                        $nilai = array_filter($detail,function($var) use($id){
                            return $var["id"] == $id;
                        });
                        $nominal=0;
                        if ($nilai) {
                            $nilai = array_values($nilai);
                            $nominal = $nilai[0]["nominal"];
                            $row .= "<td class=\"text-center\">".round($nilai[0]["skor"],2)."</td><td>".convert_currency2($nilai[0]["nominal"])."</td>";
                            $arrRow[$key][$id] = [
                                "skor"      => $nilai[0]["skor"],
                                "nominal"   => $nilai[0]["nominal"]
                            ];
                        }else{
                            $arrRow[$key][$id] = [
                                "skor"      => 0,
                                "nominal"   => 0
                            ];
                            $row .= "<td class=\"text-center\">0</td><td>0</td>";
                        }
                        $totalRow += $nominal;
                    }
                    $row .= "<td>".convert_currency2($totalRow)."</td></tr>";
                    $totalAll += $totalRow;
                }
                echo $row;
            ?>
        </tbody>
        <tfoot>
            <td></td>
            <td colspan="2">TOTAL</td>
            <?php
                foreach ($header as $key => $value) {
                    $totalSkor      = array_sum(array_column(array_column($arrRow,$value->id),'skor'));
                    $totalNominal   = array_sum(array_column(array_column($arrRow,$value->id),'nominal'));
                    echo "<td class=\"text-center\">".round($totalSkor,2)."</td><td>".convert_currency2($totalNominal)."</td>";
                }
            ?>
            <td><?=convert_currency2($totalAll)?></td>
        </tfoot>
    </table>
<pagebreak />