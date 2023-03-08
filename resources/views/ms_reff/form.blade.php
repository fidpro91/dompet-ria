<?php

use \fidpro\builder\Create;
?>
{!! Form::open(['route' => 'ms_reff.store','id'=>'form_ms_reff']) !!}
<div class="card-body">
    {!! Form::hidden('reff_id', $ms_reff->reff_id, array('id' => 'reff_id')) !!}
    {!! Create::input("reff_code",[
    "value" => $ms_reff->reff_code,

    ])->render("group");
    !!}
    {!! Create::input("reff_name",[
    "value" => $ms_reff->reff_name,
    "required" => "true"
    ])->render("group");
    !!}
    {!! 
        Create::dropDown("reff_active",[
            "data" => [
                    ["t" => "Aktif"],
                    ["f" => "Non Aktif"]
                ],
            "selected"  => $ms_reff->reff_active
        ])->render("group")
    !!}
</div>
<div class="card-footer text-center">
    {!! Form::button('New More',['class' => 'btn btn-primary btn-save','value'=>'1','type'=>'submit']) !!}
    {!! Form::button('Save',['class' => 'btn btn-success btn-save','value'=>'2','type'=>'submit']) !!}
    {!! Form::button('Cancel',['class' => 'btn btn-warning btn-refresh']); !!}
</div>
{!!Form::close()!!}

<script>
    var btnType;
    $(document).ready(() => {
        $(".btn-save").click(function(){
            btnType = $(this).val();
            if($("#reff_id").val() === ''){
                $.ajaxSetup({
                    'url'    : '{{route("ms_reff.store")}}'
                });
            }
        })
        $('#form_ms_reff').parsley().on('field:validated', function() {
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
                            'data': $('#form_ms_reff').serialize() + "&reffcat_id=" + $("#reffcat_id").val(),
                            'dataType': 'json',
                            'success': function(data) {
                                if (data.success) {
                                    Swal.fire("Sukses!", data.message, "success").then(() => {
                                        if (btnType == 2) {
                                            location.reload();
                                        }else{
                                            $('#form_ms_reff')[0].reset();
                                        }
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