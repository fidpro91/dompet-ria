@extends('templates.layout')
@section('content')
<?php

use \fidpro\builder\Bootstrap;
use \fidpro\builder\Widget;

Widget::_init(["select2"]);
?>
<div class="card border-0 shadow rounded" id="page_diklat">
    <div class="card-header">
        {!! 
            Widget::select2("unit_id",[
                "data" => [
                    "model"     => "Ms_unit",
                    "column"    => ["unit_id","unit_name"]
                ],
                "extra"     => [
                    "onchange"  => "reload_table()"
                ]
            ])->render("group","Unit Kerja Pegawai");
        !!}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table-data",[
                    "class" => "table table-hover"
                ],[
                    "url"   => "diklat/get_data_diklat",
                    "filter"    => [
                        "unit_id"   => "$('#unit_id').val()"
                    ],
                    "raw"   => [
                        '#'     => [
                            "data" => "action", 
                            "name" => "action",
                            "settings"  => [
                                "orderable" => "false", 
                                "searchable" => "false"
                            ]
                        ],
                        'indikator_skor'    => [
                            "data" => "indikator_skor", 
                            "name" => "indikator_skor",
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
                        'unit kerja' => [
                            "data" => "unit_name",
                            "name" => "mu.unit_name",
                        ],
                        'judul pelatihan' => [
                            "data" => "judul_pelatihan",
                            "name" => "dk.judul_pelatihan",
                        ]
                    ]
                ])
            }}
        </div>
    </div>
</div>

<script>
    function set_indikator(id,row) {
        var skorId = $(row).closest("tr").find(".indikator_skor").val();
        $.get("{{url('diklat/set_indikator_skor')}}/"+id+"/"+skorId,function(response){
            toastr.success(response.message, "Message : ");
            reload_table();
        },'json');
    }

    function view_file(id) {
        var url = '{{url("diklat/view_file")}}/' + id;
        window.open(url, 'PopupWindow', 'width=800,height=600');
    }

    function reload_table() {
        tb_table_data.draw();
    }
</script>
@endsection