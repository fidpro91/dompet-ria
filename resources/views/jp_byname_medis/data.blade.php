<?php
use \fidpro\builder\Bootstrap;
?>
{!! Form::hidden('komponen_id', $id_komponen, array('id' => 'komponen_id')) !!}
<div class="card border-0 shadow rounded" id="page_jp_byname_medis">
    <div class="card-header">
        {!!
            Form::button("Tambah",[
                "class" => "btn btn-primary add-form",
                "data-target" => "page_jp_byname_medis",
                "data-url" => route("jp_byname_medis.create")
            ])
        !!}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table-byname",[
                    "class" => "table table-hover"
                ],[
                    "url"       => "jp_byname_medis/get_dataTable",
                    "filter"    => [
                        "komponen_id"   => '$("#komponen_id").val()',
                        "jaspel_id"     => "jaspelId"
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
                        'emp_no','emp_name','unit_name','skor','nominal_terima'
                    ]
                ])
            }}
        </div>
    </div>
</div>