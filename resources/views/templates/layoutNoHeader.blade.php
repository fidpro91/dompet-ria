<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from coderthemes.com/adminto/layouts/light-horizontal/layouts-preloader.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 12 Feb 2020 12:51:15 GMT -->

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
<header id="topnav">
<!-- Topbar Start -->
<div class="navbar-custom">
    <div class="container-fluid">
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
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box text-center">
                        <h3 class="page-title">{{$titlePage}}</h3>
                    </div>
                </div>
            </div>
            <!-- end page title -->
            @yield('content')
            <!-- end row -->
        </div> <!-- end container -->
    </div>
    <!-- ============================================================== -->
    <!-- End Page content -->
    <!-- ============================================================== -->
</body>
<!-- Mirrored from coderthemes.com/adminto/layouts/light-horizontal/layouts-preloader.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 12 Feb 2020 12:51:15 GMT -->
</html>