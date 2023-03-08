<?php

use \fidpro\builder\Create;
use \fidpro\builder\Widget;
Widget::_init(["select2"]);
?>
{!! Form::open(['route' => 'users.store','id'=>'form_users']) !!}
<div class="card-body">
    {!! Form::hidden('id', $users->id, array('id' => 'id')) !!}
    {!! Create::input("name",[
    "value" => $users->name,
    "required" => "true"
    ])->render("group");
    !!}
    {!! Create::input("email",[
    "value" => $users->email,
    "required" => "true"
    ])->render("group");
    !!}
    {!! Create::input("password",[
    "value" => $users->password,
    "required" => "true"
    ])->render("group");
    !!}
    {!! 
        Widget::select2("emp_id",[
            "data" => [
                "model"     => "Employee",
                "filter"    => ["emp_active" => "t"],
                "column"    => ["emp_id","emp_name"]
            ]
        ])->render("group","Nama Pegawai")
    !!}
    {!! 
        Widget::select2("group_id",[
            "data" => [
                "model"     => "Ms_group",
                "filter"    => ["group_active" => "t"],
                "column"    => ["group_id","group_name"]
            ],
            "selected"  => $users->group_id
        ])->render("group","Group User")
    !!}
    {!! 
        Create::dropDown("user_active",[
            "data" => [
                ["t"     => "Ya"],
                ["f"     => "Tidak"]
            ],
            "selected"  => $users->user_active,
            "extra"     => [
                "required"  => true
            ]
        ])->render("group","User Aktif");
    !!}
</div>
<div class="card-footer text-center">
    {!! Form::submit('Save',['class' => 'btn btn-success']); !!}
    {!! Form::button('Cancel',['class' => 'btn btn-warning btn-refresh']); !!}
</div>
{!!Form::close()!!}

<script>
    $(document).ready(() => {
        $('#form_users').parsley().on('field:validated', function() {
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
                            'data': $('#form_users').serialize(),
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