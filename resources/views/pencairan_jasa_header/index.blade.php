@extends('templates.layout')
@section('content')
<?php

use App\Libraries\Servant;
use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded" id="page_pencairan_jasa_header">
    <div class="card-body">
        <?=
            Bootstrap::tabs([
                "tabs"  => [
                    "Proses Pencairan Jasa"  => [
                        "href"      => "pencairan",
                        "content"   => function(){
                            $nomor = Servant::generate_code_transaksi([
                                "text"	=> "THP/NOMOR/".date("d.m.Y"),
                                "table"	=> "pencairan_jasa_header",
                                "column"	=> "no_pencairan",
                                "delimiterFirst" => "/",
                                "delimiterLast" => "/",
                                "limit"	=> "2",
                                "number"	=> "-1",
                            ]);
                            return view("pencairan_jasa_header.form_pencairan",compact('nomor'));
                        }
                    ],
                    "Histori Pencairan Jasa"  => [
                        "href"      => "list_skor",
                        "url"       => "pencairan_jasa_header/data"
                    ]
                ]
            ]);
        ?>
    </div>
</div>
@endsection