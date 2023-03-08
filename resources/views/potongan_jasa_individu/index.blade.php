@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded" id="page_potongan_jasa_individu">
    <div class="card-header">
        {!!
            Form::button("Tambah",[
                "class" => "btn btn-primary add-form",
                "data-target" => "page_potongan_jasa_individu",
                "data-url" => route("potongan_jasa_individu.create")
            ])
        !!}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table-data",[
                    "class" => "table table-hover"
                ],[
                    "url"   => "potongan_jasa_individu/get_dataTable",
                    "raw"   => [
                        '#'     => [
                            "data" => "action", 
                            "name" => "action",
                            "settings"  => [
                                "orderable" => "false", 
                                "searchable" => "false"
                            ]
                        ],
                        'no'    => [
                            "data" => "DT_RowIndex",
                            "settings"  => [
                                "orderable" => "false", 
                                "searchable" => "false"
                            ]
                        ],
                        'nip' => [
                            "data"  => 'emp_no',
                            "name"  => 'e.emp_no'
                        ],
                        'nama pegawai' => [
                            "data"  => 'emp_name',
                            "name"  => 'e.emp_name'
                        ],
                        'nama_kategori' => [
                            "data"  => 'nama_kategori',
                            "name"  => 'kp.nama_kategori'
                        ],
                        'potongan_type' => [
                            "data"  => 'potongan_type',
                            "name"  => 'pj.potongan_type'
                        ],
                        'potongan_value'  => [
                            "data"  => 'potongan_value',
                            "name"  => 'pj.potongan_value',
                            "settings"  => [
                                "render" => "$.fn.dataTable.render.number( ',', '.', 2)"
                            ]
                        ],
                        'pot_status'  => [
                            "data"  => 'pot_status',
                            "name"  => 'pj.pot_status'
                        ],
                        'pot_note' => [
                            "data"  => 'pot_note',
                            "name"  => 'pj.pot_note'
                        ],
                        'last_angsuran'  => [
                            "data"  => 'last_angsuran',
                            "name"  => 'pj.last_angsuran'
                        ],
                        'max_angsuran'  => [
                            "data"  => 'max_angsuran',
                            "name"  => 'pj.max_angsuran'
                        ]
                    ]
                ])
            }}
        </div>
    </div>
</div>
@endsection