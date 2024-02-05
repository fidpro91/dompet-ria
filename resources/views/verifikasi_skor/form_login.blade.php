@extends('templates.layoutNoHeader')
@section('content')
<?php

use \fidpro\builder\Bootstrap;
use \fidpro\builder\Create;
use \fidpro\builder\Widget;

Widget::_init(["select2"]);
?>
<div class="card border-0 shadow rounded" id="page_login">
{!! Form::open(['url' => 'pengajuan_diklat/find','id'=>'form_diklat','enctype'=>'multipart/form-data']) !!}
<div class="card-body">
    {!! 
        Create::input("xusername",[
            "required"      => "true",
            "placeholder"   => "username aplikasi dompet ria"
        ])->render("group","Username");
    !!}
    {!! 
        Create::input("xpasssword",[
            "required"      => "true",
            "type"          => "password",
            "placeholder"   => "password aplikasi dompet ria"
        ])->render("group","Password");
    !!}
    <div class="form-group">
        <span class="captcha">{!! captcha_img() !!}</span>
        <button type="button" class="btn btn-danger" onclick="reload_capcha()" class="reload" id="reload">
            &#x21bb;
        </button>
        <p></p>
        <input class="form-control" type="text" id="capcha_log" name="capcha_log" placeholder="Ketikkan capcha" autocomplete="off">
    </div>
</div>
<div class="card-footer text-center">
    {!! Form::submit('Login',['class' => 'btn btn-success']); !!}
    {!! Form::button('Cancel',['class' => 'btn btn-warning btn-refresh']); !!}
</div>
{!!Form::close()!!}
</div>
<script>
    $('form').submit(function(){
		$.ajax({
			'data': $(this).serialize(),
			headers: {
					'X-CSRF-TOKEN': '<?=csrf_token()?>'
				},
            'beforeSend': function() {
                showLoading();
            },
			"url" 	: "{{url('login/login_verif')}}",
			'dataType': 'json',
			"type"	  : "post",
			'success': function(resp) {
				if (resp.code == "200") {
                    Swal.close();
                    $("#page_login").html(resp.content);
				}else{
                    Swal.fire("Oopss...!!", resp.message, "error");
					reload_capcha();
				}
			}
		});
      return false;
  });

  function reload_capcha() {
	$('#capcha_log').val('');
	$.get("{{url('login/reload_capcha')}}",function(resp){
		$(".captcha").html(resp.captcha);
	});
  }
</script>
@endsection