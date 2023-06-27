@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded" id="page_kategori_potongan">
    <div class="card-header">
        {!!
            Form::button("Tambah",[
                "class" => "btn btn-primary add-form",
                "data-target" => "page_kategori_potongan",
                "data-url" => route("kategori_potongan.create")
            ])
        !!}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table-data",[
                    "class" => "table table-hover"
                ],[
                    "url"       => "kategori_potongan/get_dataTable",
                    "raw"   => [
                        '#'     => [
                            "data" => "action", 
                            "name" => "action",
                            "settings"  => [
                                "width"     => "'15%'",    
                                "orderable" => "false", 
                                "searchable" => "false"
                            ]
                        ],
                        'no'    => [
                            "data" => "DT_RowIndex",
                            "orderable" => "false", 
                            "searchable" => "false"
                        ],
                        'nama_kategori','potongan_type','deskripsi_potongan','potongan_active'
                    ]
                ])
            }}
        </div>
    </div>
</div>
{{
    Bootstrap::modal('modal_component',[
        "title"   => 'Potongan Remunerasi <span id="subtitle"></span>',
        "size"    => "modal-xl"
    ])
}}
<script>
    function get_list(row,id) {
        $("#modal_component").modal("show");
        $("#modal_component").find(".modal-body").load("{{route('potongan_statis.index')}}",function(){
            var title = $(row).closest('tr').find("td").eq(2).text();
            $("#subtitle").text(title);
            $("#kategori_potongan_global").val(id);
        });
    }
</script>
@endsection