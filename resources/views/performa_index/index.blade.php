@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded" id="page_performa_index">
    <div class="card-header">
        {!!
            Form::button("Tambah",[
                "class" => "btn btn-primary add-form",
                "data-target" => "page_performa_index",
                "data-url" => route("performa_index.create")
            ])
        !!}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table-data",[
                    "class" => "table table-hover"
                ],[
                    "url"   => "performa_index/get_dataTable",
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
                        'tanggal_perform' => [
                            "data" => "tanggal_perform",
                            "name" => "pi.tanggal_perform",
                        ],
                        'emp_id','perform_id','perform_skor','perform_deskripsi','created_by','created_at','updated_at'
                    ]
                ])
            }}
        </div>
    </div>
</div>
@endsection