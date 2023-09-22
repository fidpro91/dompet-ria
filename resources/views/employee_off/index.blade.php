@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded" id="page_employee_off">
    <div class="card-header">
        {!!
        Form::button("Tambah",[
        "class" => "btn btn-primary add-form",
        "data-target" => "page_employee_off",
        "data-url" => route("employee_off.create")
        ])
        !!}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::table("table-data",[
                    "class" => "table table-hover"
                ],[
                    '#','NO','emp_nip','emp_name','unit_name','bulan_skor','periode','persentase_skor','keterangan'
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
            ajax: "{{ url('/employee_off/get_dataTable') }}",
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
                    data: 'emp_nip',
                    name: 'emp_nip',
                },
                {
                    data: 'emp_name',
                    name: 'emp_name',
                },
                {
                    data: 'unit_name',
                    name: 'unit_name',
                },
                {
                    data: 'bulan_skor',
                    name: 'bulan_skor',
                },
                {
                    data: 'periode',
                    name: 'periode',
                },
                {
                    data: 'persentase_skor',
                    name: 'persentase_skor',
                },
                {
                    data: 'keterangan',
                    name: 'keterangan',
                }
            ]
        });
    })
</script>
@endsection