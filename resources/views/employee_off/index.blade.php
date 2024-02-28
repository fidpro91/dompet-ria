@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Bootstrap;
use \fidpro\builder\Widget;

Widget::_init(["datepicker"]);
?>
<div class="card border-0 shadow rounded" id="page_employee_off">
    <div class="card-header">
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
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table-data",[
                    "class" => "table table-hover"
                ],[
                    "url"   => "employee_off/get_dataTable",
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
                    ]
                ])
            }}
        </div>
    </div>
</div>
<script>
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