<?php
use \fidpro\builder\Bootstrap;
?>
<div class="table-responsive">
    {{
        Bootstrap::DataTable("table-data",[
            "class"     => "table table-hover",
            "style"     => "width:100% !important"
        ],[
            "url"   => "pencairan_jasa_header/get_dataTable",
            "raw"   => [
                '#'     => [
                    "data" => "action", 
                    "name" => "action",
                    "settings"  => [
                        "orderable"     => "false", 
                        "searchable"    => "false",
                        "width"         => "'15%'"
                    ]
                ],
                'no'    => [
                    "data" => "DT_RowIndex",
                    "orderable" => "false", 
                    "searchable" => "false"
                ],
                'tanggal_cair','no_pencairan',
                'total_nominal' => [
                    "data"      => "total_nominal",
                    "name"      => "total_nominal",
                    "settings"  => [
                        "render" => "$.fn.dataTable.render.number( ',', '.', 2)"
                    ]
                ],
                'keterangan'
            ]
        ])
    }}
</div>