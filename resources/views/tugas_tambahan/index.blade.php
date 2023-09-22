@extends('templates.layout')
@section('content')
<?php

use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded" id="page_tugas_tambahan">
    <div class="card-header">
        {!!
        Form::button("Tambah",[
        "class" => "btn btn-primary add-form",
        "data-target" => "page_tugas_tambahan",
        "data-url" => route("tugas_tambahan.create")
        ])
        !!}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::table("table-data",[
                    "class" => "table table-hover"
                ],[
                    '#','STATUS','NO','nomor_sk','nama_tugas','pemberi_tugas','petugas','tanggal_awal','tanggal_akhir','deskripsi_tugas','jabatan_tugas','skor'
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
            ajax: "{{ url('/tugas_tambahan/get_dataTable') }}",
            columns: [{
                    data: 'action',
                    name: 'action',
                    width: '20%',
                    orderable: false,
                    searchable: false
                },
                {
                    "data": 'is_active',
                    orderable: false,
                    searchable: false
                },
                {
                    "data": 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'nomor_sk',
                    name: 'tt.nomor_sk',
                },
                {
                    data: 'nama_tugas',
                    name: 'tt.nama_tugas',
                },
                {
                    data: 'pemberi_tugas',
                    name: 'e1.emp_name',
                },
                {
                    data: 'petugas',
                    name: 'e2.emp_name',
                },
                {
                    data: 'tanggal_awal',
                    name: 'tt.tanggal_awal',
                },
                {
                    data: 'tanggal_akhir',
                    name: 'tt.tanggal_akhir',
                },
                {
                    data: 'deskripsi_tugas',
                    name: 'tt.deskripsi_tugas',
                },
                {
                    data: 'jabatan_tugas',
                    name: 'di.detail_name',
                },
                {
                    data: 'skor',
                    name: 'di.skor',
                }
            ]
        });
    })
</script>
@endsection