@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Bootstrap;
use \fidpro\builder\Widget;
use \fidpro\builder\Create;

Widget::_init(["datepicker","select2"]);
?>
<div class="card border-0 shadow rounded" id="page_komplain_skor">
    <div class="card-header">
        <div class="row">
            <div class="col-sm-3">
                {!! 
                    Widget::datepicker("bulan_skor",[
                        "format"		=>"mm-yyyy",
                        "viewMode"		=> "year",
                        "minViewMode"	=> "year",
                        "autoclose"		=> true
                    ],[
                        "readonly"      => true,
                        "value"         => date('m-Y'),
                        "onchange"      => "loadData()"
                    ])->render('group','Bulan Skor')
                !!}
            </div>
            <div class="col-md-3">
                {!! 
                    Widget::select2("unit_kerja",[
                        "data" => [
                            "model"     => "Ms_unit",
                            "filter"    => ["is_active" => "t"],
                            "column"    => ["unit_id","unit_name"]
                        ],
                        "extra"     => [
                            "onchange"      => "loadData()"
                        ]
                    ])->render("group","Unit Kerja");
                !!}
            </div>
            <div class="col-md-3">
                {!! 
                    Create::dropDown("status_komplain",[
                        "data" => [
                            ["2"     => "Sudah Diproses"],
                            ["1"     => "Belum Diproses"]
                        ],
                        "extra"     => [
                            "onchange"      => "loadData()"
                        ]
                    ])->render("group");
                !!}
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table-data",[
                    "class" => "table table-hover"
                ],[
                    "url"       => "komplain_skor/get_dataTable",
                    "filter"    => [
                        "unit_kerja"        => '$("#unit_kerja").val()',
                        "status_komplain"   => '$("#status_komplain").val()',
                        "bulan_skor"        => '$("#bulan_skor").val()'
                    ],
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
                        'tanggal','emp_no','emp_name','isi_komplain','tanggapan_komplain','status_komplain','total_skor'
                    ]
                ])
            }}
        </div>
    </div>
</div>
{{
    Bootstrap::modal('modal_komplain',[
        "title"   => 'Informasi Skor Individu#<span id="titleCom"></span>',
        "size"    => "modal-xl"
    ])
}}
<script>
    function loadData() {
        tb_table_data.draw();
    }

    function get_info(row,idkompain,idskor) {
        $("#modal_komplain").modal("show");
        $("#modal_komplain").find(".modal-body").load("{{url('komplain_skor/get_data_skor')}}/"+idskor,function(){
            var title = $(row).closest('tr').find("td").eq(4).text();
            $("#titleCom").text(title);
            $("#id_skor").val(idskor);
        });
    }
</script>
@endsection