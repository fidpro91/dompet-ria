@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded" id="page_jasa_pelayanan">
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table-data",[
                    "class" => "table table-hover"
                ],[
                    "url"   => "jasa_pelayanan/get_dataTable",
                    "raw"   => [
                        '#'     => [
                            "data"          => "action", 
                            "name"          => "action",
                            "settings"      => [
                                "orderable"     => "false", 
                                "searchable"    => "false",
                                "width"         => "'20%'"
                            ]
                        ],
                        'no'    => [
                            "data" => "DT_RowIndex",
                            "orderable" => "false", 
                            "searchable" => "false"
                        ],
                        'tanggal_jaspel','no_jasa','jaspel_bulan','jaspel_tahun','penjamin',
                        'nominal_pendapatan' => [
                            "data"      => "nominal_pendapatan",
                            "settings"  => [
                                "render"    => "$.fn.dataTable.render.number( ',', '.', 2)"
                            ]
                        ],
                        'percentase_jaspel',
                        'nominal_jaspel'     => [
                            "data"      => "nominal_jaspel",
                            "settings"  => [
                                "render"    => "$.fn.dataTable.render.number( ',', '.', 2)"
                            ]
                        ],
                        'keterangan'
                    ]
                ])
            }}
        </div>
    </div>
</div>
@endsection