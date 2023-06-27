@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded">
<?=
    Bootstrap::tabs([
        "tabs"  => [
            "Generate Skor"  => [
                "href"      => "generate_skor",
                "content"   => function(){
                    return view("skor_pegawai.generate_skor");
                }
            ],
            "List Skor Pegawai"  => [
                "href"      => "list_tindakan",
                "url"       => "skor_pegawai/get_data"
            ]
        ]
    ]);
?>
</div>
@endsection