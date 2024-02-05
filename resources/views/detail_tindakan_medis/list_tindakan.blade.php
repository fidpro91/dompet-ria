<?php
use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded" id="page_detail_tindakan_medis">
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table-data-tindakanmedis",[
                    "class" => "table table-hover"
                ],[
                    "url"       => "detail_tindakan_medis/get_dataTable",
                    "filter"    => [
                        "repo_id"   => "repoId"
                    ],
                    "raw"   => [
                        '#'     => [
                            "data" => "action", 
                            "name" => "action",
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
                        'tanggal_tindakan',
                        'nama_dokter',
                        'nama_tindakan',
                        'tarif_tindakan',
                        'klasifikasi_jasa',
                        'percentase_jasa',
                        'skor_jasa',
                        'qty_tindakan',
                        'status_bayar',
                        'jenis_tagihan',
                    ]
                ])
            }}
        </div>
    </div>
</div>