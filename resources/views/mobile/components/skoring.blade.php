<?php
use fidpro\builder\Bootstrap;
?>
<div class="table-responsive">
    {!!
        Bootstrap::tableData($skoring,[
            "class" => "table"
        ],[
            "SKOR BULAN"    => [
                "data"  => "skor_bulan",
                'custom'    => function($a){
                    return get_namaBulan($a['skor_bulan']);
                }
            ],
            "KETERANGAN"    => [
                "data"  => "keterangan"
            ],
            "SKOR"          => [
                "data"  => "skor"
            ],
            "NILAI_BRUTTO"  => [
                "data"  => "nilai_brutto",
                'custom'    => function($a){
                    return convert_currency2($a['nilai_brutto']);
                }
            ]
        ]);
    !!}
</div>