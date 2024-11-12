@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Bootstrap;
use \fidpro\builder\Widget;

Widget::_init(["datepicker","daterangepicker"]);
?>
<div class="card border-0 shadow rounded" id="page_employee_off">
    <div class="card-header">
        <div class="row">
            <div class="col-md-9">
            {!!
            Form::button("Tambah",[
                "class" => "btn btn-primary add-form",
                "data-target" => "page_employee_off",
                "data-url" => route("employee_off.create")
            ])
        !!}
        {!!
            Form::button("Update Skor",[
                "class"     => "btn btn-purple",
                "onclick"   => "update_skor()" 
            ])
        !!}
            </div>
            <div class="col-sm-3">
                    {!! 
                        Widget::datepicker("bulan_skor",[
                            "format"		=>"mm-yyyy",
                            "viewMode"		=> "months",
                            "minViewMode"	=> "months",
                            "autoclose"		=> true
                        ],[
                            "readonly"      => true,
                            "value"         => date('m-Y')
                        ])->render()
                    !!}
                </div> 
        </div>
       
    </div>
    <div class="card-body">
    <div class="row">
           
            <div class="col-md-3">
            {!! Widget::daterangePicker("periode_pegawai")->render("group","Periode Hari") !!}
            </div>
    </div>
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table-data",[
                    "class" => "table table-hover"
                ],[
                    "url"   => "employee_off/get_dataTable",
                    "filter" => ["month_filter" => "$('#bulan_skor').val()",
                                 "period" => "$('#periode_pegawai').val()"
                                 ],
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
                            "data"      => "DT_RowIndex",
                            "settings"  => [
                                "orderable" => "false", 
                                "searchable" => "false"
                            ]
                        ],
                        'emp_no','emp_name','bulan_skor','periode','persentase_skor','keterangan'
                    ],
                    "dataTable" => [
                        "order" => "[[3,'ASC']]"
                    ]
                ])
            }}
        </div>
    </div>
</div>
<script>
    $(document).ready(()=>{
       
        $("#bulan_skor,#periode_pegawai").change(()=>{
            tb_table_data.draw();
        });
    })
    function update_skor() {
        Swal.fire({
            title: 'Masukkan Bulan Skor Pegawai',
            html: '<input type="text" id="bulan_skor" class="form-control">',
            confirmButtonText: 'Update Data Skor',
            preConfirm: () => {
                return document.getElementById('bulan_skor').value;
            },
            onOpen: () => {
                // Initialize datepicker when SweetAlert is opened
                $('#bulan_skor').datepicker({
                    format: 'mm-yyyy',
                    autoclose: true,
                    todayHighlight: true,
                    "viewMode"		: "year",
                    "minViewMode"	: "year",
                });
            }
        }).then((result) => {
            $.get('{{url("employee_off/update_skor?bulan_skor=")}}'+result.value,function(resp){
                if (resp.code == 200) {
                    Swal.fire("Sukses!", resp.message, "success");
                }else{
                    Swal.fire("Oopss!", resp.message, "error");
                }
            },'json');
        });
    }
</script>
@endsection