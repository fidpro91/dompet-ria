<?php

use \fidpro\builder\Create;
use \fidpro\builder\Widget;

Widget::_init(["select2","datepicker"]);
?>
{!! Form::open(['route' => 'repository_download.store','id'=>'form_copy']) !!}
<div class="card-body">
    {!! Form::hidden('id','', array('id' => 'id')) !!}
    {!!
        Widget::datepicker("bulan_jaspel",[
            "format" =>"mm-yyyy",
            "viewMode" => "year",
            "minViewMode" => "year",
            "autoclose" =>true
        ],[
            "readonly"  => true,
            "required"  => true,
            "value"     => date('m-Y')
        ])->render("group")
    !!}
    {!!
        Widget::select2("group_penjamin",[
            "data" => [
                "model"     => "Ms_reff",
                "filter"    => ["reffcat_id" => "5"],
                "column"    => ["reff_code","reff_name"]
            ],
            "extra" => [
                "name"      => "group_penjamin[]",
                "multiple"  => "true"
            ]
        ])->render("group","Penjamin")
    !!}
</div>
<div class="card-footer text-center">
    {!! Form::submit('Save',['class' => 'btn btn-success']); !!}
    {!! Form::button('Cancel',['class' => 'btn btn-warning','data-dismiss' => 'modal']); !!}
</div>
{!!Form::close()!!}
<script>
    $(document).ready(() => {
        $('#form_copy').parsley().on('field:validated', function() {
                var ok = $('.parsley-error').length === 0;
                $('.bs-callout-info').toggleClass('hidden', !ok);
                $('.bs-callout-warning').toggleClass('hidden', ok);
            })
            .on('form:submit', function() {
                Swal.fire({
                    title: 'Copy data tindakan medis?',
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            'data': $('#form_copy').serialize(),
                            headers: {
                                'X-CSRF-TOKEN': "{{csrf_token()}}"
                            },
                            'dataType': 'json',
                            'type'  : 'post',
                            'url'   : "{{url('repository_download/copy_point')}}",
                            'beforeSend' : function(){
                                Swal.fire({
                                    html: '<i class="mdi mdi-spin mdi-loading"></i><br>Mohon tunggu....',
                                    allowOutsideClick: false,
                                    showConfirmButton: false
                                });
                            },
                            'success': function(data) {
                                if (data.code == 200) {
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