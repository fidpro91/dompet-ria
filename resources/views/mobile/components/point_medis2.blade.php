<?php
use fidpro\builder\Bootstrap;
?>
<div class="table-responsive">
    {!!
        Bootstrap::tableData($skoring,[
            "class" => "table"
        ],[
            'KETERANGAN'    => [
                'data'  => 'keterangan'
            ],
            'POINT MEDIS'   => [
                'data'  => 'point_medis'
            ],
            'NILAI BRUTTO'  => [
                'data'      => 'nilai_brutto',
                'custom'    => function($a){
                    return convert_currency2($a['nilai_brutto']);
                }
            ]
        ]);
    !!}
</div>