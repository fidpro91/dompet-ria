<?php

use \fidpro\builder\Create;
?>
{!! Form::open(['route' => 'detail_indikator.store','id'=>'form_detail_indikator']) !!}
<div class="card-body">
    {!! Form::hidden('detail_id', $detail_indikator->detail_id, array('id' => 'detail_id')) !!}
    {!! Create::input("detail_name",[
    "value" => $detail_indikator->detail_name,
    "required" => "true"
    ])->render("group");
    !!}
    {!! Create::input("detail_deskripsi",[
    "value" => $detail_indikator->detail_deskripsi,
    "required" => "true"
    ])->render("group");
    !!}
    {!! Create::input("skor",[
    "value" => $detail_indikator->skor,
    "required" => "true"
    ])->render("group");
    !!}
    {!! Create::dropDown("detail_status",[
            "data" => [
                    ["t" => "Aktif"],
                    ["f" => "Non Aktif"]
                ]
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
            if($("#detail_id").val() === ''){
                $.ajaxSetup({
                    'url'    : '{{route("detail_indikator.store")}}'
                });
            }
        })
        $('#form_detail_indikator').parsley().on('field:validated', function() {
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
                            'data': $('#form_detail_indikator').serialize()+"&indikator_id="+$("#indikator_id_global").val(),
                            'dataType': 'json',
                            'success': function(data) {
                                if (data.success) {
                                    Swal.fire("Sukses!", data.message, "success").then(() => {
                                        if (btnType == 2) {
                                            location.reload();
                                        }else{
                                            $('#form_detail_indikator')[0].reset();
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