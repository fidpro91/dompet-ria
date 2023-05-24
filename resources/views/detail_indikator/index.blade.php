<?php

use \fidpro\builder\Bootstrap;
?>
{!! Form::hidden('indikator_id_global', '', array('id' => 'indikator_id_global')) !!}
<div class="card border-0 shadow rounded" id="page_detail_indikator">
    <div class="card-header">
        {!!
        Form::button("Tambah",[
        "class" => "btn btn-primary add-form",
        "data-target" => "page_detail_indikator",
        "data-url" => route("detail_indikator.create")
        ])
        !!}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::table("table-detailIndikator",[
                    "class" => "table table-hover"
                ],[
                    '#','NO','detail_name','detail_deskripsi','skor','detail_status'
                ])
            }}
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#table-detailIndikator').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url : "{{ url('/detail_indikator/get_dataTable') }}",
                data : function(d){
                    d.indikator_id = $("#indikator_id_global").val();
                }
            },
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
                    data: 'detail_name',
                    name: 'detail_name',
                },
                {
                    data: 'detail_deskripsi',
                    name: 'detail_deskripsi',
                },
                {
                    data: 'skor',
                    name: 'skor',
                },
                {
                    data: 'detail_status',
                    name: 'detail_status',
                }
            ]
        });
    })
</script>