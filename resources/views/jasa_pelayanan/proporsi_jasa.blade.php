<?php

use \fidpro\builder\Bootstrap;
use \fidpro\builder\Widget;
use \fidpro\builder\Create;

Widget::_init(["select2", "datepicker"]);
?>
<div class="row">
    <div class="col-md-5">
        <div class="card-box data_employee" id="data_employee">
            <div class="row">
                <div class="col-md-4">
                    {!! 
                        Create::dropDown("jabatan_type",[
                            "data" => [
                                "model"     => "Ms_reff",
                                "filter"    => ["reffcat_id"  => 4],
                                "column"    => ["reff_id","reff_name"]
                            ]
                        ])->render("group","Jenis Jabatan");
                    !!}
                </div>
                <div class="col-md-4">
                    {!! 
                        Create::dropDown("is_medis",[
                            "data" => [
                                ["t"     => "Ya"],
                                ["f"     => "Tidak"]
                            ]
                        ])->render("group","Pegawai Medis");
                    !!}
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        {{
                        Bootstrap::DataTable("table-employee",[
                            "class" => "table table-hover"
                        ],[
                            "url"       => "jasa_pelayanan/get_dataTableEmployee",
                            "filter"    => [
                                "komponen_id"   => "$('#komponen_id').val()",
                                "bulan_jasa"    => "$('#bulan_pelayanan').val()",
                                "jabatan_type"  => "$('#jabatan_type').val()",
                                "is_medis"      => "$('#is_medis').val()",
                            ],
                            "raw"   => [
                                '<input type="checkbox" name="check-all" class="check-all"/>'    => [
                                    "data"      => "checkbox",
                                    "settings"  => [
                                        "orderable" => "false", 
                                        "searchable" => "false",
                                    ]
                                ],
                                'emp_no' => [
                                    "data"  => "emp_no",
                                    "name"  => "e.emp_no"
                                ],
                                'emp_name' => [
                                    "data"  => "emp_name",
                                    "name"  => "e.emp_name"
                                ],
                                'unit_name' => [
                                    "data"  => "unit_name",
                                    "name"  => "mu.unit_name"
                                ]
                            ]
                        ])
                    }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{
        Bootstrap::modal('modal_copy',[
            "title"   => 'Form Copy Proporsi',
            "size"    => "",
            "body"    => [
                            "content"   => function(){
                                return view('jasa_pelayanan.form_copy');
                            }
                        ]
        ])
    }}
    <div class="col-md-2" id="data-filter">
        {!!
            Widget::select2("komponen_id",[
            "data" => [
                "model" => "Komponen_jasa_sistem",
                "filter" => ["komponen_active" => 't'],
                "column" => ["id","nama_komponen"]
            ]
            ])->render("group","Proporsi")
        !!}
        {!!
            Widget::datepicker("bulan_pelayanan",[
                "format" =>"mm-yyyy",
                "viewMode" => "year",
                "minViewMode" => "year",
                "autoclose" =>true
            ],[
                "readonly" => true,
                "value" => date('m-Y')
            ])->render("group")
        !!}
        <div class="form-group" style="text-align: center !important;">
            <button class="btn btn-primary btn-left" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>">
                <i class="fa fas fa-angle-double-left"></i>
            </button>
            <button class="btn btn-primary btn-right" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>">
                <i class="fa fas fa-angle-double-right"></i>
            </button>
        </div>
        <div class="form-group" align="center">
            <button class="btn btn-secondary btn-block" id="btn-copy"><i class="fa fa-clone"></i> Copy</button>
            <button class="btn btn-danger btn-block" id="btn-clear"><i class="fa fa-clone"></i> Clear Data</button>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card-box data_employee_on_unit" id="data_proporsi">
            <div class="table-responsive">
                {{
                Bootstrap::DataTable("table-data-proporsi",[
                    "class" => "table table-hover"
                ],[
                    "url"   => "proporsi_jasa_individu/get_dataTable",
                    "filter"    => [
                        "komponen_id"   => "$('#komponen_id').val()",
                        "bulan_jasa"    => "$('#bulan_pelayanan').val()"
                    ],
                    "raw"   => [
                        '<input type="checkbox" name="check-all-proporsi" class="check-all"/>'    => [
                            "data"      => "checkbox",
                            "settings"  => [
                                "orderable" => "false", 
                                "searchable" => "false",
                            ]
                        ],
                        'emp_no' => [
                            "data"  => "emp_no",
                            "name"  => "e.emp_no"
                        ],
                        'emp_name' => [
                            "data"  => "emp_name",
                            "name"  => "e.emp_name"
                        ],
                        'unit_name' => [
                            "data"  => "unit_name",
                            "name"  => "mu.unit_name"
                        ] 
                    ]
                ])
            }}
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(() => {
        $("#komponen_id").change(() => {
            tb_table_data_proporsi.draw();
            tb_table_employee.draw();
        });

        $("#is_medis, #jabatan_type").change(() => {
            tb_table_employee.draw();
        });

        $("#btn-copy").click(() => {
            $("#modal_copy").modal('show');
        })

        $('body').find(".CHECK-ALL").on('click', (function() {
            if ($(this).is(":checked")) {
                $(this).closest("table").find("input[type='checkbox']").attr('checked', true);
            } else {
                $(this).closest("table").find("input[type='checkbox']").attr('checked', false);
            }
        }));

        $(".btn-right").click(function() {
            $.ajax({
                type: 'POST',
                url: '{{url("proporsi_jasa_individu/insert_right")}}',
                dataType: 'json',
                'headers': {
                    'X-CSRF-TOKEN': "<?= csrf_token() ?>"
                },
                data: $('#data_employee :input[type="checkbox"]').serialize() + '&' + $('#data-filter :input').serialize(),
                success: function(resp) {
                    if (resp.success) {
                        toastr.success(resp.message, "Message : ");
                    } else {
                        toastr.error(resp.message, "Message : ");
                    }
                    tb_table_data_proporsi.draw();
                    tb_table_employee.draw();
                }
            })
        });

        $(".btn-left").click(function() {
            $.ajax({
                type: 'POST',
                url: '{{url("proporsi_jasa_individu/insert_left")}}',
                dataType: 'json',
                'headers': {
                    'X-CSRF-TOKEN': "<?= csrf_token() ?>"
                },
                data: $('#data_proporsi :input[type="checkbox"]').serialize(),
                success: function(resp) {
                    if (resp.success) {
                        toastr.success(resp.message, "Message : ");
                    } else {
                        toastr.success(resp.message, "Message : ");
                    }
                    tb_table_data_proporsi.draw();
                    tb_table_employee.draw();
                }
            })
        });

        $("#btn-clear").click(() => {
            Swal.fire({
                title: 'Hapus Data Proporsi Jasa?',
                text: 'Sistem akan menghapus data proporsi yang belum digunakan',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        'data': $('#data-filter :input').serialize(),
                        'dataType': 'json',
                        'headers': {
                            'X-CSRF-TOKEN': "<?= csrf_token() ?>"
                        },
                        'type': 'post',
                        'url': '{{url("proporsi_jasa_individu/clear_data")}}',
                        'success': function(data) {
                            if (data.success) {
                                Swal.fire("Sukses!", data.message, "success").then(() => {
                                    tb_table_data_proporsi.draw();
                                    tb_table_employee.draw();
                                });
                            } else {
                                Swal.fire("Oopss...!!", data.message, "error");
                            }
                        }
                    });
                }
            })
        })
    })
</script>