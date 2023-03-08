@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded" id="page_employee">
    <div class="card-header">
        {!!
            Form::button("Tambah",[
                "class" => "btn btn-primary add-form",
                "data-target" => "page_employee",
                "data-url" => route("employee.create")
            ])
        !!}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table-data",[
                    "class" => "table table-hover"
                ],[
                    "url"   => "employee/get_dataTable",
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
                        'NIP' => [
                            "data" => "emp_no",
                            "name" => "e.emp_no",
                        ],
                        'nama' => [
                            "data" => "emp_name",
                            "name" => "e.emp_name",
                        ],
                        'jenis kelamin' => [
                            "data" => "emp_sex",
                            "name" => "e.emp_sex",
                        ],
                        'unit kerja' => [
                            "data" => "unit_name",
                            "name" => "mu.unit_name",
                        ],
                        'golongan' => [
                            "data" => "golongan",
                            "name" => "e.golongan",
                        ],
                        'emp_active' => [
                            "data" => "emp_active",
                            "name" => "e.emp_active",
                        ]
                    ]
                ])
            }}
        </div>
    </div>
</div>
@endsection