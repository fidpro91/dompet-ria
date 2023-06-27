<?php
use \fidpro\builder\Bootstrap;
?>
{!! Form::hidden('reffcat_id', $id, array('id' => "reffcat_id")) !!}
<div class="card border-0 shadow rounded" id="page_ms_reff">
    <div class="card-header">
        {!!
            Form::button("Tambah",[
                "class" => "btn btn-primary add-form",
                "data-target" => "page_ms_reff",
                "data-url" => route("ms_reff.create")
            ])
        !!}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table-data-$id",[
                    "class" => "table table-hover"
                ],[
                    "url"       => "ms_reff/get_dataTable",
                    "filter"    => ["reffcat_id"    => "$('#reffcat_id').val()"],
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
                        'reff_id','reff_code','reff_name','reff_active'
                    ]
                ])
            }}
        </div>
    </div>
</div>