<?php

use \fidpro\builder\Create;
?>
{!! Form::open(['route' => 'group_refference.store','id'=>'form_group_refference']) !!}
<div class="card-body">
    {!! Form::hidden('id', $group_refference->id, array('id' => 'id')) !!}
    {!! Create::input("group_reff",[
    "value" => $group_refference->group_reff,
    "required" => "true"
    ])->render("group");
    !!}
    {!! Create::input("group_desc",[
    "value" => $group_refference->group_desc
    ])->render("group");
    !!}
    {!! 
        Create::dropDown("group_reff_active",[
            "data" => [
                    ["t" => "Aktif"],
                    ["f" => "Non Aktif"]
                ],
            "selected"  => $group_refference->group_reff_active
        ])->render("group")
    !!}
</div>
<div class="card-footer text-center">
    {!! Form::submit('Save',['class' => 'btn btn-success']); !!}
    {!! Form::button('Cancel',['class' => 'btn btn-warning btn-refresh']); !!}
</div>
{!!Form::close()!!}

<script>
    $(document).ready(() => {
        $('#form_group_refference').parsley().on('field:validated', function() {
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
                            'data': $('#form_group_refference').serialize(),
                            'dataType': 'json',
                            'success': function(data) {
                                if (data.success) {
                                    Swal.fire({
                                        title: "Sukses!",
                                        text: resp.message,
                                        icon: "success",
                                        timer: 5,  // Waktu dalam milidetik sebelum SweetAlert ditutup otomatis,
                                        onClose : () => {
                                            location.reload();
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