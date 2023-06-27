@extends('templates.mobile.layout')
@section('content')
<?php
use Illuminate\Support\Facades\Auth;
?>
<div class="container">
    <div class="card mb-3">
        <div class="card-body">
            <h3>Hii..., {{Auth::user()->name}}</h3>
            <div class="testimonial-slide-three-wrapper">
                <div class="testimonial-slide3 testimonial-style3">
                    <!-- Single Testimonial Slide -->
                    <div class="single-testimonial-slide">
                        <div class="text-content">
                            <h6 class="mb-2">Welcome to DOMPET-RIA APP.</h6><span class="d-block">Application Of Remunerasi</span>
                        </div>
                    </div>
                    <!-- Single Testimonial Slide -->
                    <div class="single-testimonial-slide">
                        <div class="text-content">
                            <h6 class="mb-2">RSUD IBNU SINA KABUPATEN GRESIK.</h6><span class="d-block">Jl. DR. Wahidin Sudiro Husodo No.243B</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container direction-rtl">
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-4">
                    <a href="{{url('mobile/jasa_pelayanan')}}" class="feature-card mx-auto text-center">
                        <div class="card mx-auto bg-gray">
                            <img src="{{asset('mobile/img/demo-img/remunerasi.png')}}" alt="">
                        </div>
                        <p class="mb-0">REMUNERASI</p>
                    </a>
                </div>
                <div class="col-4">
                    <a href="{{url('mobile/profil')}}" class="feature-card mx-auto text-center">
                        <div class="card mx-auto bg-gray"><img src="{{asset('mobile/img/demo-img/profil.png')}}" alt=""></div>
                        <p class="mb-0">INFO PRIBADI</p>
                    </a>
                </div>
                <div class="col-4">
                    <a href="#" class="feature-card mx-auto text-center">
                        <div class="card mx-auto bg-gray"><img src="{{asset('mobile/img/demo-img/tugas.png')}}" alt=""></div>
                        <p class="mb-0">TUGAS TAMBAHAN</p>
                    </a>
                </div>
                <div class="col-4">
                    <div class="feature-card mx-auto text-center">
                        <div class="card mx-auto bg-gray"><img src="{{asset('mobile/img/demo-img/sertifikat.png')}}" alt=""></div>
                        <p class="mb-0">SERTIFIKASI</p>
                    </div>
                </div>
                <div class="col-4">
                    <a href="{{url('mobile/jasa_pelayanan/monitoring_remun')}}" class="feature-card mx-auto text-center">
                        <div class="card mx-auto bg-gray"><img src="{{asset('mobile/img/demo-img/monitor.png')}}" alt=""></div>
                        <p class="mb-0">Detail Remunerasi</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection