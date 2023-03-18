<?php
use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded" id="page_performa_index">
    <div class="card-header">
        {!!
            Form::button("Tambah",[
                "class" => "btn btn-primary add-form",
                "data-target" => "page_performa_index",
                "data-url" => url("performa_index/create/$performa_id")
            ])
        !!}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table-data",[
                    "class" => "table table-hover"
                ],[
                    "url"       => "performa_index/get_dataTable",
                    "filter"    => ["performa_id" => $performa_id],
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
                        'tanggal_perform'=> [
                            "data" => "tanggal_perform",
                            "name" => "pi.tanggal_perform"
                        ],
                        'emp_no' => [
                            "data" => "emp_no",
                            "name" => "e.emp_no"
                        ],
                        'emp_name'=> [
                            "data" => "emp_name",
                            "name" => "e.emp_name"
                        ],
                        'detail_name'=> [
                            "data" => "detail_name",
                            "name" => "di.detail_name"
                        ],
                        'skor'=> [
                            "data" => "skor",
                            "name" => "di.skor"
                        ],
                        'perform_deskripsi' => [
                            "data" => "perform_deskripsi",
                            "name" => "pi.perform_deskripsi"
                        ]
                    ]
                ])
            }}
        </div>
    </div>
</div>