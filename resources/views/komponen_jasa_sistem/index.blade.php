<?php
use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded" id="page_komponen_jasa_sistem">
    <div class="card-header">
        {!!
            Form::button("Tambah",[
                "class" => "btn btn-primary add-form",
                "data-target" => "page_komponen_jasa_sistem",
                "data-url" => route("komponen_jasa_sistem.create")
            ])
        !!}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table-komponen-sistem",[
                    "class" => "table table-hover"
                ],[
                    "url"   => "komponen_jasa_sistem/get_dataTable",
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
                        'kode_komponen','nama_komponen','percentase_jasa','deskripsi_komponen','komponen_active'
                    ]
                ])
            }}
        </div>
    </div>
</div>