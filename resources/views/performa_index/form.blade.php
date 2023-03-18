<?php

use \fidpro\builder\Create;
use fidpro\builder\Widget;
Widget::_init(["select2","datepicker"]);
?>
{!! Form::open(['route' => 'performa_index.store','id'=>'form_performa_index']) !!}
<div class="card-body">
    {!! Form::hidden('id', $performa_index->id, array('id' => 'id')) !!}
    {!! Form::hidden('perform_id', $performa_index->perform_id, array('id' => 'perform_id')) !!}
    {!! 
        Widget::datepicker("tanggal_perform",[
            "format"		=>"dd-mm-yyyy",
            "autoclose"		=>true
        ],[
            "readonly"      => true,
            "value"         => $performa_index->tanggal_perform
        ])->render("group","Tanggal")
    !!}
    {!! 
        Widget::datepicker("expired_date",[
            "format"		=>"dd-mm-yyyy",
            "autoclose"		=>true
        ],[
            "readonly"      => true,
            "value"         => $performa_index->expired_date
        ])->render("group","Berlaku Sampai Tanggal")
    !!}
    {!! 
        Widget::select2("emp_id",[
            "data" => [
                "model"     => "Employee",
                "filter"    => ["emp_active"  => "t"],
                "column"    => ["emp_id","emp_name"]
            ],
            "selected"  => $performa_index->emp_id,
            "extra"     => [
                "required"  => true
            ]
        ])->render("group");
    !!}
    {!! 
        Create::dropDown("perform_skor",[
            "data" => [
                "model"     => "Detail_indikator",
                "filter"    => ["indikator_id" => $performa_index->indikator_id],
                "column"    => ["detail_id","detail_name"]
            ],
            "selected"  => $performa_index->perform_skor,
            "extra"     => [
                "required"  => true
            ]
        ])->render("group");
    !!}
    {!! Create::input("perform_deskripsi",[
    "value" => $performa_index->perform_deskripsi
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
        $('#form_performa_index').parsley().on('field:validated', function() {
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
                            'data': $('#form_performa_index').serialize(),
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