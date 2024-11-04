@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded" id="page_table_rekap_absen">
    <div class="card-header">
        {!!
            Form::button("Tambah Manual",[
                "class" => "btn btn-primary add-form",
                "data-target" => "page_table_rekap_absen",
                "data-url" => route("table_rekap_absen.create")
            ])
        !!}
        {!!
            Form::button("Get Data Prestige",[
                "class" => "btn btn-purple",
                "data-target" => "page_table_rekap_absen",
                "data-url" => route("table_rekap_absen.create")
            ])
        !!}
        {!!
            Form::button("Insert Kedisiplinan",[
                "class" => "btn btn-warning",
                "data-target" => "page_table_rekap_absen",
                "data-url" => route("table_rekap_absen.create")
            ])
        !!}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table-data",[
                    "class" => "table table-hover"
                ],[
                    "url"   => "table_rekap_absen/get_dataTable",
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
                        'nip','nama_pegawai','bulan_update','tahun_update','persentase_kehadiran','keterangan'
                    ]
                ])
            }}
        </div>
    </div>
</div>
@endsection