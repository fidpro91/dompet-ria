@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Bootstrap;
use fidpro\builder\Widget;
Widget::_init(["datepicker"]);
?>
<div class="card border-0 shadow rounded" id="page_jasa_pelayanan">
    <div class="card-body" >
        <div class="row">
            <div class="col-md-4">
            {!! 
                        Widget::datepicker("filter_bulan",[
                            "format"		=>"mm-yyyy",
                            "viewMode"		=> "months",
                            "minViewMode"	=> "months",
                            "autoclose"		=> true
                        ],[
                            "readonly"      => true,
                            "value"         => date('m-Y')
                        ])->render("group"," FILTER BULAN ")
                    !!}
            </div>
       
        </div>
        <div class="table-responsive">
            <div style="min-width: 1300px !important;">
                {{
                    Bootstrap::DataTable("table-data-jaspel",[
                        "class" => "table table-hover"
                    ],[
                        "url"   => "jasa_pelayanan/get_dataTable",
                        "filter" => [
                                        "bulan" => "$('#filter_bulan').val()"
                                    ],
                        "raw"   => [
                            '#'     => [
                                "data"          => "action", 
                                "name"          => "action",
                                "settings"      => [
                                    "orderable"     => "false", 
                                    "searchable"    => "false",
                                    "width"         => "'10%'"
                                ]
                            ],
                            'no'    => [
                                "data" => "DT_RowIndex",
                                "orderable" => "false", 
                                "searchable" => "false"
                            ],
                            'tanggal_jaspel','no_jasa',
                            'bulan' => [
                                "data"  => "jaspel_bulan",
                                "name"  => "jaspel_bulan"
                            ],
                            'penjamin',
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
                            ]
                        ],
                        "dataTable" => [
                            "order"         => "[[2,'desc']]",
                            "autoWidth"     => "false"
                        ]
                    ])
                }}
            </div>
        </div>
    </div>
</div>
{{
    Bootstrap::modal('modal_report',[
        "title"   => 'Cetak',
        "size"    => "modal-sm",
        "body"    => [
            "content" => function() {
                return view("jasa_pelayanan.button_print");
            }
        ]
    ])
}}
{{
    Bootstrap::modal('modal_edit',[
        "title"   => 'Form Edit Perhitungan Jasa Pelayanan',
        "size"    => "modal-xxl",
        "body"    => []
    ])
}}
<script>
    $(document).ready(()=>{
        $("#filter_bulan").change(()=>{
            tb_table_data_jaspel.draw();
        });
    })

    function open_print(id) {
        $("#modal_report").modal("show");
        $("#modal_report").find("#jaspel_id").val(id);
    }
    var jaspelId;
    function set_editable(id) {
        jaspelId = id;
        $("#modal_edit").modal("show");
        $("#modal_edit").find(".modal-body").load('{{url("jp_byname_medis/index")}}/'+id);
    }
</script>
@endsection