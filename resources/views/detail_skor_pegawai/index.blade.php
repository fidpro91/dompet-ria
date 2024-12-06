<?php
use \fidpro\builder\Bootstrap;
?>
 {!! Form::hidden('kodeskor', $kodeskor, array('id' => 'kodeskor')) !!}
<div class="card border-0 shadow rounded" id="page_detail_skor_pegawai">
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table-detailskor",[
                    "class" => "table table-hover"
                ],[
                    "url"       => "detail_skor_pegawai/get_dataTable",
                    "filter"    => [
                        "kode_skor"    => '$("#kodeskor").val()',
                        "id_skor"      => '$("#id_skor").val()'
                    ],
                    "raw"   => [
                        '#'     => [
                            "data" => "action", 
                            "name" => "action", 
                            "orderable" => "false", 
                            "searchable" => "false"
                        ],
                        'no'    => [
                            "data" => "DT_RowIndex",
                            "orderable" => "false", 
                            "searchable" => "false"
                        ],
                        'detail_skor','skor'
                    ]
                ])
            }}
        </div>
    </div>
</div>