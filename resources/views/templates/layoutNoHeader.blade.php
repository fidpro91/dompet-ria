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

<body class="menubar-dark">
    <!-- End Preloader-->
    <!-- ============================================================== -->
    <!-- Start Page Content here -->
    <!-- ============================================================== -->

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
    <!-- ============================================================== -->
    <!-- End Page content -->
    <!-- ============================================================== -->
</body>
<!-- Mirrored from coderthemes.com/adminto/layouts/light-horizontal/layouts-preloader.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 12 Feb 2020 12:51:15 GMT -->
</html>