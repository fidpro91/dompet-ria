@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded" id="page_potongan_jasa_medis">
    <div class="card-header">
        {!!
            Form::button("Tambah",[
                "class" => "btn btn-primary add-form",
                "data-target" => "page_potongan_jasa_medis",
                "data-url" => route("potongan_jasa_medis.create")
            ])
        !!}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table-data",[
                    "class" => "table table-hover"
                ],[
                    "url"   => "potongan_jasa_medis/get_dataTable",
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
                        'potongan_id','pencairan_id','potongan_nama','jasa_brutto','penghasilan_pajak','percentase_pajak','potongan_value','medis_id_awal','akumulasi_penghasilan_pajak','master_potongan','kategori_id','header_id'
                    ]
                ])
            }}
        </div>
    </div>
</div>
@endsection