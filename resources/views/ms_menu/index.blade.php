@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded" id="page_ms_menu">
    <div class="card-header">
        {!!
            Form::button("Tambah",[
                "class" => "btn btn-primary add-form",
                "data-target" => "page_ms_menu",
                "data-url" => route("ms_menu.create")
            ])
        !!}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::table("table-data",[
                    "class" => "table table-hover"
                ],[
                    '#','NO','menu_id','menu_code','menu_name','menu_url','menu_parent_id','menu_status','menu_icon','slug'
                ])
            }}
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#table-data').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url('/ms_menu/get_dataTable') }}",
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
                    data : 'menu_id',
                    name : 'menu_id',
                },
{
                    data : 'menu_code',
                    name : 'menu_code',
                },
{
                    data : 'menu_name',
                    name : 'menu_name',
                },
{
                    data : 'menu_url',
                    name : 'menu_url',
                },
{
                    data : 'menu_parent_id',
                    name : 'menu_parent_id',
                },
{
                    data : 'menu_status',
                    name : 'menu_status',
                },
{
                    data : 'menu_icon',
                    name : 'menu_icon',
                },
{
                    data : 'slug',
                    name : 'slug',
                }
            ]
        });
    })
</script>
@endsection