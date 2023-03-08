@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded">
<?=
    Bootstrap::tabs([
        "tabs"  => [
            "Proporsi Jasa Pelayanan"  => [
                "href"      => "proporsi",
                "content"   => function(){
                    return view("jasa_pelayanan.proporsi_jasa");
                }
            ],
            "Skor Individu Pegawai"  => [
                "href"      => "list_skor",
                "content"   => function(){
                    return view("jasa_pelayanan.skor_individuRemun");
                }
            ],
            "Hitung Jasa Pelayanan"  => [
                "href"      => "hitung_jasa",
                "content"   => function(){
                    return view("jasa_pelayanan.form_hitung");
                }
            ]
        ]
    ]);
?>
</div>
@endsection