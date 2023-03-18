@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Bootstrap;
?>
<div class="row">
    
    <div class="col-md-5">
        <div class="card card-inverse text-white">
            <img class="card-img img-fluid" src="{{asset('assets/images/icon-system/reporting.gif')}}" alt="Card image">
        </div>
    </div>
    <div class="col-md-7">
        <div class="card border-0 shadow rounded">
        <?=
            Bootstrap::tabs([
                "tabs"  => [
                    "Pajak & Potongan Tetap Pegawai"  => [
                        "href"      => "pajak",
                        "content"   => function(){
                            return view("laporan.laporan_pajak");
                        }
                    ],
                    "Potongan Lain-lain"  => [
                        "href"      => "potongan",
                        "content"   => function(){
                            return view("laporan.laporan_potongan");
                        }
                    ]
                ]
            ]);
        ?>
        </div>
    </div>
</div>
@endsection