@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Bootstrap;
use \fidpro\builder\Widget;
use \fidpro\builder\Create;

Widget::_init(["datepicker","select2"]);
?>
<style>
.badge-lg {
    font-size: 0.85rem; /* Ukuran teks */
    padding: 0.2rem 0.25rem; /* Padding vertikal dan horizontal */
    border-radius: 0.2rem; /* Opsional: untuk sudut lebih membulat */
}
hr {
    border: 1px solid;
}
</style>
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
                        "value"         => date('m-Y',strtotime('-1 month')),
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
                        'no'    => [
                            "data"          => "DT_RowIndex",
                            "width"         => "5%",
                            "orderable"     => "false", 
                            "searchable"    => "false"
                        ],
                        'nama pegawai' => [
                            "data"          => "emp_name",
                            "width"         => "25%",
                        ],
                        'isi_komplain' => [
                            "data"          => "isi_komplain",
                            "width"         => "55%",
                            "orderable"     => "false", 
                            "searchable"    => "false"
                        ]
                        ,'total_skor'
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

    function set_temp(row) {
        let template = $.trim($(row).text());
        $(row).closest('td').find(".tanggapan_komplain").text(template);
        $(row).closest('td').find(".tanggapan_komplain").focus();
    }
    
    function send_response(row) {
        const data = $(row).closest('td').find(".form_komplain_skor").serialize()
        $.ajax({
            'data': data,
            'method' : 'POST',
            'url'  : '{{route("komplain_skor.update_data")}}',
            'dataType': 'json',
            'success': function(data) {
                if (data.success) {
                    toastr.success(data.message, 'Berhasil');
                    loadData();
                }else{
                    Swal.fire("Oopss...!!", data.message, "error");
                }
            }
        });
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