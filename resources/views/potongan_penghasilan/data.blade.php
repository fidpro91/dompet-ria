<?php
use \fidpro\builder\Bootstrap;
?>
{!! Form::hidden('kategori_potongan', $kategori_potongan, array('id' => 'kategori_potongan')) !!}
<div class="card border-0 shadow rounded" id="page_potongan_penghasilan">
    <div class="card-header">
        {!!
            Form::button("Generate Potongan",[
                "class" => "btn btn-primary add-form",
                "data-target"       => "page_potongan_penghasilan",
                "data-url"          => route("potongan_penghasilan.create"),
                "data-url-store"    => route("potongan_penghasilan.store")
            ])
        !!}
        {!!
            Form::button("Tambah Per Pegawai",[
                "class" => "btn btn-warning add-form",
                "data-target"       => "page_potongan_penghasilan",
                "data-url"          => url("potongan_jasa_medis/create?id_header=".request()->segment(3)),
                "data-url-store"    => route("potongan_jasa_medis.store")
            ])
        !!}
        {!!
            Form::button("Delete All",[
                "class" => "btn btn-danger",
                "onclick"           => "delete_all()"
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
                        "nomor_rekening"  => [
                            "data"  => "nomor_rekening",
                            "name"  => "e.nomor_rekening"
                        ],
                        "nama pegawai"  => [
                            "data"  => "emp_name",
                            "name"  => "e.emp_name"
                        ],
                        "unit_kerja"  => [
                            "data"  => "unit_kerja",
                            "name"  => "mu.unit_name"
                        ],
                        "golongan"      => [
                            "data"  => "golongan",
                            "name"  => "e.golongan"
                        ], 
                        "kode_ptkp"     => [
                            "data"  => "kode_ptkp",
                            "name"  => "e.kode_ptkp"
                        ], 
                        "gaji_pokok"     => [
                            "data"  => "gaji_pokok",
                            "name"  => "e.gaji_pokok",
                            "settings"  => [
                                "render"    => "$.fn.dataTable.render.number( ',', '.', 2)", 
                                "orderable" => "false", 
                                "searchable" => "false"
                            ]
                        ], 
                        "jasa_brutto"     => [
                            "data"  => "jasa_brutto",
                            "name"  => "pm.jasa_brutto",
                            "settings"  => [
                                "render"    => "$.fn.dataTable.render.number( ',', '.', 2)", 
                                "orderable" => "false", 
                                "searchable" => "false"
                            ]
                        ],
                        "akumulasi_pendapatan"      => [
                            "data"  => "akumulasi_pendapatan",
                            "name"  => "pm.akumulasi_pendapatan",
                            "settings"  => [
                                "render"    => "$.fn.dataTable.render.number( ',', '.', 2)", 
                                "orderable" => "false", 
                                "searchable" => "false"
                            ]
                        ],
                        "penghasilan_pajak"     => [
                            "data"  => "penghasilan_pajak",
                            "name"  => "pm.penghasilan_pajak",
                            "settings"  => [
                                "render"    => "$.fn.dataTable.render.number( ',', '.', 2)"
                            ]
                        ], 
                        "percentase_pajak"     => [
                            "data"  => "percentase_pajak",
                            "name"  => "pm.percentase_pajak"
                        ], 
                        "potongan_value"     => [
                            "data"  => "potongan_value",
                            "name"  => "pm.potongan_value",
                            "settings"  => [
                                "render"    => "$.fn.dataTable.render.number( ',', '.', 2)"
                            ]
                        ]
                    ],
                    "dataTable" => [
                        "order"         => "[[4,'asc'],[3,'asc']]",
                        "dom"           => "'Bfrtip'",
                        "buttons"       => "[
                            'csv', 'excel', 'pdf', 'print'
                        ]",
                    ]
                ])
            }}
        </div>
    </div>
</div>

<script>
    function delete_all() {
        Swal.fire({
                title: 'Hapus Data Potongan Jasa?',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        'data': {
                            kategori_potongan : $("#kategori_potongan").val(),
                            id_cair : $("#id_cair").val()
                        },
                        'headers': {
                            'X-CSRF-TOKEN': "<?= csrf_token() ?>"
                        },
                        'dataType': 'json',
                        'type'    : 'post',
                        'url'   : '{{url("potongan_penghasilan/destroy_all")}}',
                        'beforeSend': function() {
                            showLoading();
                        },
                        'success': function(data) {
                            if (data.success) {
                                Swal.fire({
                                    title: "Sukses!",
                                    text: data.message,
                                    type: "success",
                                    timer: 1500,  // Waktu dalam milidetik sebelum SweetAlert ditutup otomatis
                                    onClose : () => {
                                        location.reload();
                                    }
                                });
                            }else{
                                Swal.fire("Oopss...!!", data.message, "error");
                            }
                        }
                    });
                }
            })
    }
</script>