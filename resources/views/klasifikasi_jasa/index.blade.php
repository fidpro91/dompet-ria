<?php
use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded" id="page_klasifikasi_jasa">
    <div class="card-header">
        {!!
            Form::button("Tambah",[
                "class" => "btn btn-primary add-form",
                "data-target" => "page_klasifikasi_jasa",
                "data-url" => route("klasifikasi_jasa.create")
            ])
        !!}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table-data-klasifikasi",[
                    "class" => "table table-hover"
                ],[
                    "url"   => "klasifikasi_jasa/get_dataTable",
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
                        'id_klasifikasi_jasa','klasifikasi_jasa','percentase_eksekutif','percentase_non_eksekutif'
                    ]
                ])
            }}
        </div>
    </div>
</div>