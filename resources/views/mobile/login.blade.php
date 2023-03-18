<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Affan - PWA Mobile HTML Template">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#0134d4">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <!-- The above 4 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <!-- Title -->
    <title>Affan - PWA Mobile HTML Template</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" href="img/core-img/favicon.ico">
    <link rel="apple-touch-icon" href="img/icons/icon-96x96.png">
    <link rel="apple-touch-icon" sizes="152x152" href="img/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="167x167" href="img/icons/icon-167x167.png">
    <link rel="apple-touch-icon" sizes="180x180" href="img/icons/icon-180x180.png">
    @include('templates.mobile.components.css')
    <!-- Web App Manifest -->
    <!-- <link rel="manifest" href="manifest.json"> -->
  </head>
  <body>
    <!-- Preloader -->
    <div id="preloader">
      <div class="spinner-grow text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
    </div>
    <!-- Internet Connection Status -->
    <!-- # This code for showing internet connection status -->
    <div class="internet-connection-status" id="internetStatus"></div>
    <!-- Login Wrapper Area -->
    <div class="login-wrapper d-flex align-items-center justify-content-center">
      <div class="custom-container">
        <!-- <div class="text-center px-4"><img class="login-intro-img" src="img/bg-img/36.png" alt=""></div> -->
        <!-- Register Form -->
        <div class="register-form mt-4">
          <h6 class="mb-3 text-center">Log in to continue to Affan.</h6>
          <form action="#" id="formLogin" onsubmit="login()">
            <div class="form-group">
              <input class="form-control" name="username" type="text" placeholder="Username">
            </div>
            <div class="form-group position-relative">
              <input class="form-control" name="password" id="psw-input" type="password" placeholder="Enter Password">
              <div class="position-absolute" id="password-visibility"><i class="bi bi-eye"></i><i class="bi bi-eye-slash"></i></div>
            </div>
            <button class="btn btn-primary w-100" type="submit">Sign In</button>
          </form>
        </div>
      </div>
    </div>
    @include('templates.mobile.components.javascript')
    @stack('js')
    <script>
      function login() {
        // let formData = new new FormData(document.getElementById('formLogin')[0]);
        /* fetch("{{url('mobile/loginact')}}",{
            body: formData,
            method: "post"
        })
        .then((response) => response.json())
        .then((data) => {
            alert(data.message);
        })
        .catch((error) => {
            console.warn(error);
        }); */
        return false;
      }
    </script>
  </body>
</html>