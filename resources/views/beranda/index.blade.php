@extends('templates.layout')
@section('content')
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card-box">
            <h4 class="header-title mt-0 mb-3">Kepegawaian</h4>
            <div class="widget-box-2">
                <div class="widget-detail-2 text-right">
                    <div class="avatar-lg float-left mr-3">
                        <img src="{{asset('assets/images/icon-system/pngegg.png')}}" class="img-fluid rounded-circle" alt="user">
                    </div>
                    <h2 class="font-weight-normal mb-1"> {{$chart['pegawai']}} </h2>
                    <p class="text-muted mb-3">Pegawai Aktif</p>
                </div>
                <div class="progress progress-bar-alt-success progress-sm">
                    <div class="progress-bar bg-success" role="progressbar" aria-valuenow="77" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                        <span class="sr-only">77% Complete</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card-box">
            <h4 class="header-title mt-0 mb-3">Penjamin Pelayanan</h4>
            <div class="widget-box-2">
                <div class="widget-detail-2 text-right">
                    <div class="avatar-lg float-left mr-3">
                        <img src="{{asset('assets/images/icon-system/asuransi.png')}}" class="img-fluid rounded-circle" alt="user">
                    </div>
                    <h2 class="font-weight-normal mb-1"> {{$chart['penjamin']}} </h2>
                    <p class="text-muted mb-3">Penjamin Aktif</p>
                </div>
                <div class="progress progress-bar-alt-success progress-sm">
                    <div class="progress-bar bg-success" role="progressbar" aria-valuenow="77" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                        <span class="sr-only">77% Complete</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card-box">
            <h4 class="header-title mt-0 mb-3">Klasifikasi Jasa</h4>
            <div class="widget-box-2">
                <div class="widget-detail-2 text-right">
                    <div class="avatar-lg float-left mr-3">
                        <img src="{{asset('assets/images/icon-system/klasifikasi.png')}}" class="img-fluid rounded-circle" alt="user">
                    </div>
                    <h2 class="font-weight-normal mb-1"> {{$chart['klasifikasi']}} </h2>
                    <p class="text-muted mb-3">Klasifikasi Jasa</p>
                </div>
                <div class="progress progress-bar-alt-success progress-sm">
                    <div class="progress-bar bg-success" role="progressbar" aria-valuenow="77" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                        <span class="sr-only">77% Complete</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card-box">
            <h4 class="header-title mt-0 mb-3">Remunerasi</h4>
            <div class="widget-box-2">
                <div class="widget-detail-2 text-right">
                    <div class="avatar-lg float-left mr-3">
                        <img src="{{asset('assets/images/icon-system/complete.png')}}" class="img-fluid rounded-circle" alt="user">
                    </div>
                    <h2 class="font-weight-normal mb-1"> {{$chart['remunrasi']}} </h2>
                    <p class="text-muted mb-3">Remunerasi Sukses</p>
                </div>
                <div class="progress progress-bar-alt-success progress-sm">
                    <div class="progress-bar bg-success" role="progressbar" aria-valuenow="77" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                        <span class="sr-only">77% Complete</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow rounded">
            <div class="card-body">
                {!! $chart['statistik']->container() !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow rounded">
            <div class="card-body">
                {!! $chart['last_remun']->container() !!}
            </div>
        </div>
    </div>
</div>
{!! $chart['statistik']->script() !!}
{!! $chart['last_remun']->script() !!}
@endsection