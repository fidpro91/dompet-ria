@extends('templates.layout')
@section('content')
<?php

use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded" id="page_diklat">
    <div class="card-header">
        {!!
            Form::button("Tambah",[
            "class" => "btn btn-primary add-form",
            "data-target" => "page_diklat",
            "data-url" => route("diklat.create")
            ])
        !!}
        <a href="{{url('diklat/verifikasi_diklat')}}" class="btn btn-purple">
            <span class="badge badge-danger rounded-circle">
                {{$totalPengajuan}}
            </span>
            Verifikasi Skor Diklat
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::table("table-data",[
                    "class" => "table table-hover"
                ],[
                    '#','NO','id','dari_tanggal','sampai_tanggal','judul_pelatihan','penyelenggara','indikator_skor','skor','peserta','lokasi_pelatihan'
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
            ajax: "{{ url('/diklat/get_dataTable') }}",
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
                    data: 'id',
                    name: 'id',
                },
                {
                    data: 'dari_tanggal',
                    name: 'dari_tanggal',
                },
                {
                    data: 'sampai_tanggal',
                    name: 'sampai_tanggal',
                },
                {
                    data: 'judul_pelatihan',
                    name: 'judul_pelatihan',
                },
                {
                    data: 'penyelenggara',
                    name: 'penyelenggara',
                },
                {
                    data: 'detail_name',
                    name: 'detail_name',
                },
                {
                    data: 'skor',
                    name: 'skor',
                },
                {
                    data: 'emp_name',
                    name: 'emp_name',
                },
                {
                    data: 'lokasi_pelatihan',
                    name: 'lokasi_pelatihan',
                }
            ]
        });
    })
</script>
@endsection