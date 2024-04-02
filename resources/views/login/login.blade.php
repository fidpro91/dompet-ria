<!DOCTYPE html>
<html lang="en">
<head>
	<title>Login {{config('app.name')}}</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="{{asset('assets/login_temp')}}/images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/login_temp')}}/vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/login_temp')}}/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/login_temp')}}/fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/login_temp')}}/vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="{{asset('assets/login_temp')}}/vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/login_temp')}}/vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/login_temp')}}/vendor/select2/select2.min.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="{{asset('assets/login_temp')}}/vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/login_temp')}}/css/util.css">
	<link rel="stylesheet" type="text/css" href="{{asset('assets/login_temp')}}/css/main.css">
<!--===============================================================================================-->
</head>
<style>
	input.upper { text-transform: uppercase; }
</style>
<body>
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login100-form-title" style="background-image: url({{asset('assets/login_temp')}}/images/img_front.jpg);">
					<span class="login100-form-title-1">
						DOMPET RIA
					</span>
				</div>

				<form class="login100-form validate-form">
					<div class="wrap-input100 validate-input m-b-26" data-validate="Username is required">
						<span class="label-input100">Username</span>
						<input class="input100" type="text" name="email_log" placeholder="Enter username" autocomplete="off">
						<span class="focus-input100"></span>
					</div>

					<div class="wrap-input100 validate-input m-b-18" data-validate = "Password is required">
						<span class="label-input100">Password</span>
						<input class="input100" type="password" name="password_log" placeholder="Enter password">
						<span class="focus-input100"></span>
					</div>
					<div class="wrap-input100 validate-input m-b-18" data-validate = "Capcha is required">
						{!! htmlFormSnippet() !!}
					</div> 
					<div class="container-login100-form-btn">
						<button class="login100-form-btn btn-block">
							Login
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	
<!--===============================================================================================-->
	<script src="{{asset('assets/login_temp')}}/vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="{{asset('assets/login_temp')}}/vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="{{asset('assets/login_temp')}}/vendor/bootstrap/js/popper.js"></script>
	<script src="{{asset('assets/login_temp')}}/vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="{{asset('assets/login_temp')}}/vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
	<script src="{{asset('assets/login_temp')}}/js/main.js"></script>
	{!! ReCaptcha::htmlScriptTagJsApi() !!}
<script>
  $('form').submit(function(){
		$.ajax({
			'data': $(this).serialize(),
			headers: {
					'X-CSRF-TOKEN': '<?=csrf_token()?>'
				},
			"url" 	: "{{route('actionlogin')}}",
			'dataType': 'json',
			"type"	  : "post",
			'success': function(resp) {
				if (resp.code == "200") {
					window.location.assign(resp.redirect);
				}else{
					alert(resp.message);
					location.reload();
				}
			}
		});
      return false;
  });
</script>
</body>
</html>