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
                        'emp_no'    => [
                            "data"  => "emp_no",
                            "name"  => "e.emp_no"
                        ],
                        'emp_name'  => [
                            "data"  => "emp_name",
                            "name"  => "e.emp_name"
                        ],
                        'unit_name' => [
                            "data"  => "unit_name",
                            "name"  => "mu.unit_name"
                        ],
                        'skor'  => [
                            "data"  => "skor",
                            "name"  => "jm.skor"
                        ],
                        'nominal_terima'     => [
                            "data"      => "nominal_terima",
                            "name"      => "jm.nominal_terima",
                            "settings"  => [
                                "render"    => "$.fn.dataTable.render.number( ',', '.', 2)"
                            ]
                        ],
                    ],
                    "dataTable" => [
                        "order"         => "[[4,'asc'],[3,'asc']]",
                        "autoWidth"     => "false"
                    ]
                ])
            }}
        </div>
    </div>
</div>