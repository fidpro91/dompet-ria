<table class="table" border="1">
    <tr>
        <th >NO</th>
        <th >NO. REK</th>
        <th >NAMA</th>
        <th >GOLONGAN</th>
        <th >UNIT KERJA</th>
        <th >BRUTTO</th>
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
            <td>".str_replace('&','Dan',$value->unit_name)."</td>
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
        <td colspan="4">TOTAL</td>
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