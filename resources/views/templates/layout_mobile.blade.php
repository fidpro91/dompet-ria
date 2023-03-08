<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from coderthemes.com/adminto/layouts/light-horizontal/layouts-preloader.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 12 Feb 2020 12:51:15 GMT -->

<head>
    <meta charset="utf-8" />
    <title>{{config('app.name')}} - Responsive Admin Dashboard Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    @include('templates.components.css')
    @include('templates.components.javascript')
</head>

<body class="menubar-dark">
<?php

$name = explode(' ',Auth::user()->name);
if (count($name)>1) {
    $name = $name[0].' '.$name[1];
}else{
    $name = Auth::user()->name;
}
?>
    <!-- Pre-loader -->
    <div id="preloader">
        <div id="status">
            <div class="spinner">Loading...</div>
        </div>
    </div>
    <!-- End Preloader-->

    <!-- Navigation Bar-->
    <!-- ============================================================== -->
    <!-- Start Page Content here -->
    <!-- ============================================================== -->

    <div class="wrapper">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                        </div>
                        <h4 class="page-title">{!!$pageName!!}</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->
            <!-- end row -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="row">
                            <div class="col-xl-3 col-md-6">
                                <div class="card-box">
                                    <div class="widget-detail-2 text-center">
                                        <div class="avatar-sm mr-6">
                                            <img src="{{asset('assets/images/icon-system/pngegg.png')}}" class="img-fluid rounded-circle" alt="user">
                                        </div>
                                    </div>
                                    <h4 class="header-title mt-0 mb-3">Kepegawaian</h4>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card-box text-center">
                                    <div class="avatar-sm mr-6">
                                        <img src="{{asset('assets/images/icon-system/pngegg.png')}}" class="img-fluid rounded-circle" alt="user">
                                    </div>
                                    <h4 class="header-title mt-0 mb-3">Kepegawaian</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- end container -->
    </div>
    <!-- end wrapper -->
    <!-- ============================================================== -->
    <!-- End Page content -->
    <!-- ============================================================== -->
    @include('templates.footer')
</body>

<!-- Mirrored from coderthemes.com/adminto/layouts/light-horizontal/layouts-preloader.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 12 Feb 2020 12:51:15 GMT -->

</html>