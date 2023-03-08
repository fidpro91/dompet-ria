<?php

use \fidpro\builder\Create;
use \fidpro\builder\Widget;
Widget::_init(["select2","datepicker"]);
?>
{!! Form::open(['route' => 'employee_off.store','id'=>'form_employee_off']) !!}
<div class="card-body">
    {!! Form::hidden('id', $employee_off->id, array('id' => 'id')) !!}
    {!! 
        Widget::select2("emp_id",[
            "data" => [
                "model"     => "Employee",
                "column"    => ["emp_id","emp_name"]
            ],
            "selected"  => $employee_off->emp_id
        ])->render("group","Nama Pegawai")
    !!}
    {!! 
        Widget::datepicker("bulan_jasa_awal",
        [
            "format"		=>"mm-yyyy",
            "viewMode"		=> "year",
            "minViewMode"	=> "year",
            "autoclose"		=>true
        ],[
            "required"      => true,
            "readonly"      => true,
            "value"         => date('m-Y')
        ])->render("group","Bulan Awal Off")
    !!}
    {!! 
        Widget::datepicker("bulan_jasa_akhir",
        [
            "format"		=>"mm-yyyy",
            "viewMode"		=> "year",
            "minViewMode"	=> "year",
            "autoclose"		=>true
        ],[
            "required"      => true,
            "readonly"      => true,
            "value"         => date('m-Y')
        ])->render("group","Bulan Akhir Off")
    !!}
    {!! 
        Create::input("keterangan",[
        "value" => $employee_off->keterangan,

        ])->render("group");
    !!}
</div>
<div class="card-footer text-center">
    {!! Form::submit('Save',['class' => 'btn btn-success']); !!}
    {!! Form::button('Cancel',['class' => 'btn btn-warning btn-refresh']); !!}
</div>
{!!Form::close()!!}
<script>
    $(document).ready(() => {
        $('#form_employee_off').parsley().on('field:validated', function() {
                var ok = $('.parsley-error').length === 0;
                $('.bs-callout-info').toggleClass('hidden', !ok);
                $('.bs-callout-warning').toggleClass('hidden', ok);
            })
            .on('form:submit', function() {
                Swal.fire({
                    title: 'Simpan Data?',
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            'data': $('#form_employee_off').serialize(),
                            'dataType': 'json',
                            'success': function(data) {
                                Swal.fire("Sukses!", data.message, "success").then(() => {
                                    location.reload();
                                });
                            }
                        });
                    }
                })
                return false;
            });
    })
</script>