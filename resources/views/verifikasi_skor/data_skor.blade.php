<?php
use fidpro\builder\Create;

$getHeader = new ArrayIterator($skorPegawai);
$header = json_decode($getHeader->current()->detail);
?>
<table class="table table-hover">
    <thead>
        <tr>
            <th rowspan="2">#</th>
            <th rowspan="2">NO</th>
            <th rowspan="2">NIP</th>
            <th rowspan="2">NAMA</th>
            <th rowspan="2">UNIT KERJA</th>
            <?php
            foreach ($header as $key => $value) {
                echo "<th colspan=\"2\">" . strtoupper($value->kode) . "</th>";
            }
            ?>
            <th rowspan="2">TOTAL SKOR</th>
            <th rowspan="2">SKOR REVISI</th>
            <th rowspan="2">KETERANGAN</th>
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
        $row = '';
        $totalSkor = 0;
        foreach ($skorPegawai as $key => $value) {
            $button = Create::action("<i class=\"fas fa-check\"></i>", [
                "class"     => "btn btn-success btn-xs",
                "onclick"   => "konfirmasi_skor($value->id)",
            ]);

            if (!$value->id_komplain) {
                $button .= Create::action("<i class=\"mdi mdi-wechat\"></i>", [
                    "class"     => "btn btn-secondary btn-xs",
                    "onclick"   => "keluhan_skor($value->id)",
                ]);
            }
            $row .= '
                    <tr>
                        <td>' . $button . '</td>
                        <td>' . ($key + 1) . '</td>
                        <td>' . $value->emp_no . '</td>
                        <td>' . $value->emp_name . '</td>
                        <td>' . $value->unit_name . '</td>';
            foreach ($header as $x => $res) {
                $detail = json_decode($value->detail, true);
                $kode = $res->kode;
                $detail = array_filter($detail, function ($var) use ($kode) {
                    return $var["kode"] == $kode;
                });
                $detail = array_values($detail);
                $row .= '
                            <td class="center">' . ($detail[0]['skor'] ?? 0) . '</td>
                            <td>' . ($detail[0]['keterangan'] ?? "") . '</td>
                        ';
            }
            $row .= '<td>' . $value->total_skor . '</td>';
            $row .= '<td>' . $value->skor_koreksi . '</td>';
            $row .= '<td>' . $value->keterangan . '</td>';
            $row .= '</tr>';
            $totalSkor += $value->total_skor;
        }
        echo $row;
        ?>
    </tbody>
    <tfoot>
        <tr>
            <td></td>
            <td colspan="20">TOTAL SKOR</td>
            <td><?= $totalSkor ?></td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
</table>