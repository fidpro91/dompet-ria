@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded" id="page_komponen_jasa">
<?=
            Bootstrap::tabs([
                "tabs"  => [
                    "Komponen Jasa"  => [
                        "href"      => "komponen_jasa",
                        "content"   => function(){
                            return view("komponen_jasa.data");
                        }
                    ],
                    "Setting Komponen Jasa"  => [
                        "href"      => "komponen_jasa_sistem",
                        "url"       => "komponen_jasa_sistem"
                    ],
                    "Klasifikasi Tindakan Medis"  => [
                        "href"      => "list_tindakan",
                        "url"       => "klasifikasi_jasa"
                    ]
                ]
            ]);
        ?>
</div>
@endsection