<?php

use \fidpro\builder\Create;
?>
{!! Form::open(['route' => 'potongan_statis.store','id'=>'form_potongan_statis']) !!}
<div class="card-body">
    {!! Form::hidden('pot_stat_id', $potongan_statis->pot_stat_id, array('id' => 'pot_stat_id')) !!}
    {!! Create::input("pot_stat_code",[
    "value"     => $potongan_statis->pot_stat_code,
    "required"  => true
    ])->render("group");
    !!}
    {!! 
        Create::input("nama_potongan",[
        "value" => $potongan_statis->nama_potongan,
        "required" => "true"
        ])->render("group");
    !!}
    {!! 
        Create::dropDown("potongan_type",[
            "data" => [
                ["1"     => "Percentase"],
                ["2"     => "Nominal"]
            ],
            "selected"  => $potongan_statis->potongan_type
        ])->render("group")
    !!}
    {!! Create::input("potongan_nominal",[
    "value" => $potongan_statis->potongan_nominal,
    "required" => "true"
    ])->render("group");
    !!}
    {!! 
        Create::dropDown("pot_status",[
            "data" => [
                ["t"     => "Aktif"],
                ["f"     => "Non Aktif"]
            ],
            "selected"  => $potongan_statis->pot_status
        ])->render("group")
    !!}
    {!! 
        Create::input("potongan_note",[
        "value" => $potongan_statis->potongan_note
        ])->render("group");
    !!}
</div>
<div class="card-footer text-center">
    {!! Form::button('New More',['class' => 'btn btn-primary btn-save','value'=>'1','type'=>'submit']) !!}
    {!! Form::button('Save',['class' => 'btn btn-success btn-save','value'=>'2','type'=>'submit']) !!}
    {!! Form::button('Cancel',['class' => 'btn btn-warning btn-refresh']); !!}
</div>
{!!Form::close()!!}

<script>
    $(".btn-save").click(function(){
        btnType = $(this).val();
        if($("#pot_stat_id").val() === ''){
            $.ajaxSetup({
                'url'    : '{{route("potongan_statis.store")}}'
            });
        }
    })
    $(document).ready(() => {
        $('#form_potongan_statis').parsley().on('field:validated', function() {
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
                            'data': $('#form_potongan_statis').serialize()+"&kategori_potongan="+$("#kategori_potongan_global").val(),
                            'dataType': 'json',
                            'success': function(data) {
                                if (data.success) {
                                    Swal.fire("Sukses!", data.message, "success").then(() => {
                                        if (btnType == 2) {
                                            location.reload();
                                        }else{
                                            $('#form_potongan_statis')[0].reset();
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