<?php
use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded" id="page_repository_download">
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table-data",[
                    "class" => "table table-hover"
                ],[
                    "url"   => "repository_download/get_dataTable",
                    "raw"   => [
                        '#'     => [
                            "data"      => "action", 
                            "name"      => "action",
                            "settings"  => [
                                "orderable" => "false", 
                                "searchable" => "false"
                            ]
                        ],
                        'no'    => [
                            "data" => "DT_RowIndex",
                            "settings"  => [
                                "orderable" => "false", 
                                "searchable" => "false"
                            ]
                        ],
                        'id','download_date','download_no','bulan_jasa','bulan_pelayanan','periode_awal','periode_akhir','group_penjamin','jenis_pembayaran'
                    ],
                    "dataTable" => [
                        "order"    => "[[3,'desc']]"
                    ]
                ])
            }}
        </div>
    </div>
</div>