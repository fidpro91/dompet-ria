@extends('templates.layout')
@section('content')
<?php

use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded" id="page_indikator">
    <div class="card-header">
        {!!
        Form::button("Tambah",[
        "class" => "btn btn-primary add-form",
        "data-target" => "page_indikator",
        "data-url" => route("indikator.create")
        ])
        !!}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::table("table-data",[
                    "class" => "table table-hover"
                ],[
                    '#','NO','id','kode_indikator','indikator','deskripsi','bobot','status'
                ])
            }}
        </div>
    </div>
</div>
{{
    Bootstrap::modal('modal_component',[
        "title"   => 'Komponen Indikator Skor <span id="titleCom"></span>',
        "size"    => "modal-xl"
    ])
}}
<script type="text/javascript">
    $(document).ready(function() {
        $('#table-data').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url('/indikator/get_dataTable') }}",
            columns: [{
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
                {
                    "data": 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'kode_indikator',
                    name: 'kode_indikator',
                },
                {
                    data: 'indikator',
                    name: 'indikator',
                },
                {
                    data: 'deskripsi',
                    name: 'deskripsi',
                },
                {
                    data: 'bobot',
                    name: 'bobot',
                },
                {
                    data: 'status',
                    name: 'status',
                }
            ]
        });
    })

    function get_list(row,id) {
        $("#modal_component").modal("show");
        $("#modal_component").find(".modal-body").load("{{route('detail_indikator.index')}}",function(){
            var title = $(row).closest('tr').find("td").eq(4).text();
            $("#titleCom").text(title);
            $("#indikator_id_global").val(id);
        });
    }
</script>
@endsection