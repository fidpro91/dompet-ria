@extends('templates.layout')
@section('content')
<div class="card-box task-detail">
    <div class="dropdown float-right">
        <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
            <i class="mdi mdi-dots-vertical"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end"
            style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(20px, 8px, 0px);">
            <!-- item-->
            <a href="javascript:void(0);" class="dropdown-item">Action</a>
            <!-- item-->
            <a href="javascript:void(0);" class="dropdown-item">Another action</a>
            <!-- item-->
            <a href="javascript:void(0);" class="dropdown-item">Something else</a>
            <!-- item-->
            <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
        </div>
    </div>
    <div class="media mb-3">
        <img class="d-flex mr-3 rounded-circle avatar-md" alt="64x64" src="assets/images/users/user-2.jpg">
        <div class="media-body">
            <h4 class="media-heading mt-0">Bahan Data Pembagian Jasa Pelayanan</h4>
            <span class="badge badge-danger">Urgent</span>
        </div>
    </div>

    <h4>Code HTML email template for welcome email</h4>
    <p class="text-muted">
        Consectetur adipisicing elit. Voluptates, illo, iste
        itaque voluptas corrupti ratione reprehenderit magni similique Tempore quos
        delectus asperiores libero voluptas quod perferendis erum ipsum dolor sit.
    </p>
    <div class="row task-dates mb-0 mt-2">
        <div class="col-lg-6">
            <h5 class="font-600 m-b-5">Tanggal Awal Pelayanan</h5>
            <p> 22 March 2016 <small class="text-muted">1:00 PM</small></p>
        </div>

        <div class="col-lg-6">
            <h5 class="font-600 m-b-5">Tanggal Selesai Pelayanan</h5>
            <p> 17 April 2016 <small class="text-muted">12:00 PM</small></p>
        </div>
    </div>
    <div class="clearfix"></div>

    <div class="task-tags mt-2">
        <h5>Penjamin</h5>
        <div class="bootstrap-tagsinput">
            <span class="tag label label-warning">Amsterdam</span>
        </div>
    </div>

    <div class="attached-files mt-4">
        <div class="row">
            <div class="col-sm-12">
                <div class="text-right m-t-30">
                    <button type="submit" class="btn btn-success waves-effect waves-light">
                        Prosess Perhitungan
                    </button>
                    <button type="button" class="btn btn-light waves-effect">Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection