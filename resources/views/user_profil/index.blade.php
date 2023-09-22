@extends('templates.layout')
@section('content')
<?php

use \fidpro\builder\Create;
use \fidpro\builder\Widget;

Widget::_init(["select2", "datepicker", "inputmask"]);
?>
<div class="card border-0 shadow rounded">
    {!! Form::open(['route' => 'employee.store','id'=>'form_employee']) !!}
    <div class="card-body">
        {!! Form::hidden('emp_id', $employee->emp_id, array('id' => 'emp_id')) !!}
        <div class="row">
            <img id="photo_preview" src="{{asset('storage/uploads/photo_pegawai/'.$employee->photo);}}" class="rounded-circle avatar-xl img-thumbnail float-left mr-3" alt="profile-image">
            <div class="form-group">
                    <label for="photo">Foto Pegawai</label>
                    {!!
                    Create::upload("photo",[
                    "value" => $employee->photo
                    ])->render();
                    !!}
                </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                {!!
                    Create::input("emp_no",[
                    "value" => $employee->emp_no,
                    ])->render("group","NIP");
                !!}
                {!! 
                    Create::input("emp_noktp",[
                    "value" => $employee->emp_noktp
                    ])->render("group","NOMOR KTP");
                !!}
                {!! 
                    Create::input("emp_nokk",[
                    "value" => $employee->emp_nokk
                    ])->render("group","NOMOR KK");
                !!}
                {!! 
                    Create::input("emp_name",[
                    "value" => $employee->emp_name,
                    "required" => "true"
                ])->render("group","NAMA PEGAWAI");
                !!}
                {!!
                Widget::datepicker("emp_birthdate",[
                "format" =>"dd-mm-yyyy",
                "autoclose" =>true
                ],[
                "readonly" => true,
                "value" => date_indo($employee->emp_birthdate)
                ])->render("group","Tanggal Lahir")
                !!}
                {!!
                Create::dropDown("emp_sex",[
                "data" => [
                ["L" => "Laki-laki"],
                ["P" => "Perempuan"]
                ],
                "selected" => $employee->emp_sex,
                "extra" => [
                "required" => true
                ]
                ])->render("group","Jenis Kelamin");
                !!}
                {!! Create::input("nomor_rekening",[
                "value" => $employee->nomor_rekening,
                "required" => true
                ])->render("group");
                !!}
                {!! 
                    Create::input("email",[
                        "value" => $employee->email
                    ])->render("group");
                !!}
                {!! 
                    Create::input("password",[
                        "type"  => "password",
                        "value" => $employee->emp_noktp
                    ])->render("group","PASSWORD");
                !!}
                {!! 
                    Create::input("phone",[
                    "value" => $employee->phone
                    ])->render("group","Nomor Telp/Whatsapp");
                !!}
            </div>
        </div>
    </div>
    <div class="card-footer text-center">
        {!! Form::submit('Save',['class' => 'btn btn-success btn-block']); !!}
    </div>
</div>
{!!Form::close()!!}
<script>
    $(document).ready(() => {
        $('#photo').change(function() {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#photo_preview').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });
        $('#form_employee').parsley().on('field:validated', function() {
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
                        var formData = new FormData($("#form_employee")[0]);
                        $.ajax({
                            'data': formData,
                            headers: {
                                'X-CSRF-TOKEN': '<?= csrf_token() ?>'
                            },
                            'url'        : '{{url("user_profil/update_data")}}',
                            'type'       : 'post',
                            'processData': false,
                            'contentType': false,
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
@endsection