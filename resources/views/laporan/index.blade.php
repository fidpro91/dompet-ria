@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded" id="page_komponen_jasa">
<?=
            Bootstrap::tabs([
                "tabs"  => [
                    "Pajak Pegawai"  => [
                        "href"      => "komponen_jasa",
                        "content"   => function(){
                            return view("laporan.laporan_pajak");
                        }
                    ],
                    "Potongan Lain-lain"  => [
                        "href"      => "komponen_jasa",
                        "content"   => function(){
                            // return view("laporan.laporan_potongan");
                        }
                    ]
                ]
            ]);
        ?>
</div>
@endsection