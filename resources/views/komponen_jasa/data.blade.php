<?php
use \fidpro\builder\Bootstrap;
?>
<div class="card-header">
        {!!
            Form::button("Tambah",[
                "class" => "btn btn-primary add-form",
                "data-target" => "page_komponen_jasa",
                "data-url" => route("komponen_jasa.create")
            ])
        !!}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table-data",[
                    "class" => "table table-hover"
                ],[
                    "url"   => "komponen_jasa/get_dataTable",
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
                        'komponen_id','komponen_kode','komponen_nama','komponen_percentase','has_detail','komponen_parent','is_vip','has_child'
                    ]
                ])
            }}
        </div>
    </div>