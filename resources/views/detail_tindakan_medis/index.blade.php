@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded" id="page_detail_tindakan_medis">
    <div class="card-body">
        <?=
            Bootstrap::tabs([
                "tabs"  => [
                    "Kroscek Tindakan Belum Termapping"  => [
                        "href"      => "kroscek_tindakan",
                        "content"   => function(){
                            return view("detail_tindakan_medis.form_kroscek");
                        }
                    ],
                    "Download Data"  => [
                        "href"      => "download",
                        "content"   => function(){
                            return view("detail_tindakan_medis.form_download");
                        }
                    ],
                    "Repository Download"  => [
                        "href"      => "repo_download",
                        "url"       => route("repository_download.index")
                    ]
                ]
            ]);
        ?>
    </div>
</div>
<table class="table">
    <thead>
        <tr>

        </tr>
    </thead>
    <tbody>
        
    </tbody>
</table>
@endsection