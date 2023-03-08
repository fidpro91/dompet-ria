<?php
use \fidpro\builder\Bootstrap;
?>
{!! Form::hidden('kategori_potongan_global','', array('id' => 'kategori_potongan_global')) !!}
<div class="card border-0 shadow rounded" id="page_potongan_statis">
    <div class="card-header">
        {!!
            Form::button("Tambah",[
                "class" => "btn btn-primary add-form",
                "data-target" => "page_potongan_statis",
                "data-url" => route("potongan_statis.create")
            ])
        !!}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table-data-potongan",[
                    "class" => "table table-hover"
                ],[
                    "url"   => "potongan_statis/get_dataTable",
                    "filter"    => [
                        "kategori_id"     => "$('#kategori_potongan_global').val()"
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
                        'pot_stat_code','nama_potongan','potongan_type','potongan_nominal','pot_status','potongan_note'
                    ]
                ])
            }}
        </div>
    </div>
</div>