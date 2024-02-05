@extends('templates.layoutNoHeader')
@section('content')
<?php

use \fidpro\builder\Bootstrap;
use \fidpro\builder\Create;
use \fidpro\builder\Widget;

Widget::_init(["select2"]);
?>
<div class="card border-0 shadow rounded" id="page_diklat">
{!! Form::open(['url' => 'pengajuan_diklat/find','id'=>'form_diklat','enctype'=>'multipart/form-data']) !!}
<div class="card-body">
    {!! 
        Widget::select2("peserta_id",[
            "data" => [
                "model"     => "Employee",
                "filter"    => ["emp_active" => "t"],
                "column"    => ["emp_id","emp_name"]
            ]
        ])->render("group","Nama Peserta")
    !!}
    {!! 
        Create::input("nomor_rekening",[
            "required" => "true"
        ])->render("group");
    !!}
    <div class="form-group">
        <label for="">NO. WA/TELP</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">(+62)</span>
            </div>
            <input type="text" id="phone" name="phone" class="form-control" placeholder="Nomor Wa tanpa angka 0 didepan. Ex : 8576768882">
            <div class="input-group-append">
                <button class="btn btn-dark waves-effect waves-light" type="button" onclick="send_otp()">Kirim kode (OTP)</button>
            </div>
        </div>
    </div>
    {!! 
        Create::input("kode_otp",[
            "required"      => "true",
            "placeholder"   => "Masukkan kode OTP yang dikirim lewat whatsapp anda"
        ])->render("group","Kode OTP :");
    !!}
    {!! 
        Create::input("email",[
            "required" => "true"
        ])->render("group","Alamat E-mail");
    !!}
</div>
<div class="card-footer text-center">
    {!! Form::submit('Cari Data',['class' => 'btn btn-success']); !!}
    {!! Form::button('Cancel',['class' => 'btn btn-warning btn-refresh']); !!}
</div>
{!!Form::close()!!}
</div>
<script>
    function send_otp() {
        $.ajax({
            'data': {
                phone : $("#phone").val(),
            },
            headers: {
                'X-CSRF-TOKEN': '<?=csrf_token()?>'
            },
            'beforeSend': function() {
                showLoading();
            },
            'type'    : 'post',
            'url'     : '{{url("pengajuan_diklat/send_otp")}}',
            'dataType': 'json',
            'success': function(data) {
                if (data.code == 200) {
                    Swal.fire("Sukses!", data.message, "success")
                }else{
                    Swal.fire("Oopss...!!", data.message, "error");
                }
            }
        });
    }
</script>
@endsection