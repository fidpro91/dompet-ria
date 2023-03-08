<?php

use \fidpro\builder\Create;
use \fidpro\builder\Widget;

Widget::_init(["select2","datepicker"]);
?>
{!! Form::open(['route' => 'skor_pegawai.store','id'=>'form_skor_pegawai']) !!}
<div class="card-body">
    {!! Form::hidden('id', $skor_pegawai->id, array('id' => 'id')) !!}
    <div class="row">
        <div class="col-md-4">
            {!!
                Widget::select2("emp_id",[
                    "data" => [
                        "model" => "Employee",
                        "filter" => ["emp_active" => "t"],
                        "column" => ["emp_id","emp_name"]
                    ],
                    "extra" => [
                        "required"  => true
                    ]
                ])->render("group","Nama Pegawai")
            !!}
            {!! 
                Widget::datepicker("bulan_update",
                [
                    "format"		=>"mm-yyyy",
                    "viewMode"		=> "year",
                    "minViewMode"	=> "year",
                    "autoclose"		=>true
                ],[
                    "required"      => true,
                    "readonly"      => true,
                    "value"         => ($skor_pegawai->bulan_update??date('m-Y'))
                ])->render("group","Bulan Skor")
            !!}
            {!! 
                Create::input("basic_index",[
                "value" => $skor_pegawai->basic_index,
                "class" => "form-control hitung"
                ])->render("group");
            !!}
            {!! 
                Create::input("capacity_index",[
                "value" => $skor_pegawai->capacity_index,
                "class" => "form-control hitung"
                ])->render("group");
            !!}
        </div>
        <div class="col-md-4">
            {!! 
                Create::input("emergency_index",[
                "value" => $skor_pegawai->emergency_index,
                "class" => "form-control hitung"
                ])->render("group");
            !!}
            {!! 
                Create::input("unit_risk_index",[
                "value" => $skor_pegawai->unit_risk_index,
                "class" => "form-control hitung"
                ])->render("group");
            !!}
            {!! 
                Create::input("position_index",[
                "value" => $skor_pegawai->position_index,
                "class" => "form-control hitung"
                ])->render("group");
            !!}
            {!! 
                Create::input("competency_index",[
                "value" => $skor_pegawai->competency_index,
                "class" => "form-control hitung"
                ])->render("group");
            !!}
        </div>
        <div class="col-md-4">
            {!! 
                Create::input("admin_risk_index",[
                "value" => $skor_pegawai->admin_risk_index,
                "class" => "form-control hitung"
                ])->render("group");
            !!}
            {!! 
                Create::input("persentase_skor")->render("group");
            !!}
            {!! 
                Create::input("total_skor",[
                "value" => $skor_pegawai->total_skor
                ])->render("group");
            !!}
        </div>
    </div>
</div>
<div class="card-footer text-center">
    {!! Form::submit('Save',['class' => 'btn btn-success']); !!}
    {!! Form::button('Cancel',['class' => 'btn btn-warning btn-refresh']); !!}
</div>
{!!Form::close()!!}

<script>
    $(document).ready(() => {
        $(".hitung").change(function(){
            let total=0;
            $('body').find('.hitung').each(function(){
                total += parseFloat($.isNumeric($(this).val())?$(this).val():0);
            });
            $("#total_skor").val(total);
        });
        $("#persentase_skor").change(function(){
            let persen = parseFloat($(this).val());
            let total  = persen/100*parseFloat($("#total_skor").val());
            $("#total_skor").val(total);
        });
        $('#form_skor_pegawai').parsley().on('field:validated', function() {
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
                            'data': $('#form_skor_pegawai').serialize(),
                            'dataType': 'json',
                            'success': function(data) {
                                if (data.success) {
                                    Swal.fire("Sukses!", data.message, "success").then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire("Oopss...!!", data.message, "error");
                                }
                            }
                        });
                    }
                })
                return false;
            });
    })
</script>