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
                    '#','NO','nomor_sk','nama_tugas','pemberi_tugas','petugas','tanggal_awal','tanggal_akhir','deskripsi_tugas','jabatan_tugas','skor'
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
                    name: 'nomor_sk',
                },
                {
                    data: 'nama_tugas',
                    name: 'nama_tugas',
                },
                {
                    data: 'pemberi_tugas',
                    name: 'pemberi_tugas',
                },
                {
                    data: 'petugas',
                    name: 'petugas',
                },
                {
                    data: 'tanggal_awal',
                    name: 'tanggal_awal',
                },
                {
                    data: 'tanggal_akhir',
                    name: 'tanggal_akhir',
                },
                {
                    data: 'deskripsi_tugas',
                    name: 'deskripsi_tugas',
                },
                {
                    data: 'jabatan_tugas',
                    name: 'jabatan_tugas',
                },
                {
                    data: 'skor',
                    name: 'skor',
                }
            ]
        });
    })
</script>
@endsection