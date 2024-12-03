<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>{{config('app.name')}} - {{ strtoupper(str_replace('_',' ',Request::segment(1))) }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    @include('templates.components.css')
    @include('templates.components.javascript')
</head>
<style>
    .navbar-custom {
        background-color: #D6FDFF !important;
    }
</style>
<script>
    var showLoading = function() {
        Swal.fire({
            html: '<i class="mdi mdi-spin mdi-loading"></i><br>Mohon tunggu....',
            allowOutsideClick: false,
            showConfirmButton: false
        });
  };
</script>
<?php
$name = explode(' ',Auth::user()->name);
if (count($name)>1) {
    $name = $name[0].' '.$name[1];
}else{
    $name = Auth::user()->name;
}
?>
<header id="topnav">
<!-- Topbar Start -->
<div class="navbar-custom">
    <div class="container-fluid">
        <ul class="list-unstyled topnav-menu float-right mb-0">
            <li class="dropdown notification-list">
                <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect" data-toggle="dropdown" href="/#" role="button" aria-haspopup="false" aria-expanded="false">
                    <img src="{{ asset('assets/themes')}}/assets/images/users/user-1.jpg" alt="user-image" class="rounded-circle">
                    <span class="pro-user-name ml-1">
                        <?=$name?> <i class="mdi mdi-chevron-down"></i>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                    <!-- item-->
                    <div class="dropdown-header noti-title">
                        <h6 class="text-overflow m-0">Welcome !</h6>
                    </div>
                    <!-- item-->
                    <a href="{{url('user_profil')}}" class="dropdown-item notify-item">
                        <i class="fe-user"></i>
                        <span>My Account</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <!-- item-->
                    <a href="{{url('logout')}}" class="dropdown-item notify-item">
                        <i class="fe-log-out"></i>
                        <span>Logout</span>
                    </a>

                </div>
            </li>
        </ul>
        <!-- LOGO -->
        <div class="logo-box">
            <a href="{{url('/')}}" class="logo text-center">
                <span class="logo-lg">
                    <img src="{{ asset('assets/images/logo.webp')}}" alt="" height="50">
                    <!-- <span class="logo-lg-text-light">UBold</span> -->
                </span>
                <span class="logo-sm">
                    <!-- <span class="logo-sm-text-dark">U</span> -->
                    <img src="{{ asset('assets/themes')}}/assets/images/logo-sm.png" alt="" height="24">
                </span>
            </a>
        </div>

    </div> <!-- end container-fluid-->
</div>
<!-- end Topbar -->
</header>

<body class="menubar-dark">
    <!-- End Preloader-->
    <!-- ============================================================== -->
    <!-- Start Page Content here -->
    <!-- ============================================================== -->
    <div class="wrapper" style="padding-top: 70px !important;">
        <!-- <div class="container-fluid"> -->
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box text-center">
                        <h2>{{$titlePage}}</h2>
                    </div>
                </div>
            </div>
            <!-- end page title -->
            @yield('content')
            <!-- end row -->
        <!-- </div> end container -->
    </div>
    <!-- ============================================================== -->
    <!-- End Page content -->
    <!-- ============================================================== -->
</body>
</html>