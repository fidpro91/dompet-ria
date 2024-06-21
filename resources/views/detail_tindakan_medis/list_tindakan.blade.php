<?php
use \fidpro\builder\Bootstrap;
use \fidpro\builder\Widget;

Widget::_init(["select2"]);
?>
<div class="card border-0 shadow rounded" id="page_detail_tindakan_medis">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                {!!
                    Widget::select2("filter_dokter",[
                        "data" => [
                            "model"     => "Employee",
                            "filter"    => [
                                "is_medis"  => "t"
                            ],
                            "column"    => ["emp_id","emp_name"]
                        ]
                    ])->render("group","Nama Dokter")
                !!}
            </div>
        </div>
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table-data-tindakanmedis",[
                    "class" => "table table-hover"
                ],[
                    "url"       => "detail_tindakan_medis/get_dataTable",
                    "filter"    => [
                        "repo_id"   => "repoId",
                        "dokter_id" => '$("#filter_dokter").val()'
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
<script>
    $(document).ready(()=>{
        $("#filter_dokter").on("change",function(){
            tb_table_data_tindakanmedis.draw();
        });
    })
</script>