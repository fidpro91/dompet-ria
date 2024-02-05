<?php
use \fidpro\builder\Bootstrap;
?>
{!! Form::hidden('kategori_potongan', $kategori_potongan, array('id' => 'kategori_potongan')) !!}
<div class="card border-0 shadow rounded" id="page_potongan_penghasilan">
    <div class="card-header">
        {!!
            Form::button("Tambah",[
                "class" => "btn btn-primary add-form",
                "data-target"       => "page_potongan_penghasilan",
                "data-url"          => route("potongan_penghasilan.create"),
                "data-url-store"    => route("potongan_penghasilan.store")
            ])
        !!}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table-data",[
                    "class" => "table table-hover"
                ],[
                    "url"       => "potongan_penghasilan/get_dataTable",
                    "filter"    => [
                        "kategori_potongan" => '$("#kategori_potongan").val()',
                        "id_cair"           => '$("#id_cair").val()',
                    ],
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
                        'pajak_no','id_cair_header','kategori_potongan',
                        'total_potongan'     => [
                            "data"      => "total_potongan",
                            "settings"  => [
                                "render"    => "$.fn.dataTable.render.number( ',', '.', 2)"
                            ]
                        ],'potongan_method'
                    ]
                ])
            }}
        </div>
    </div>
</div>