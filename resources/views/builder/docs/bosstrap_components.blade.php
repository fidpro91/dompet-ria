@extends('builder.template.index')
@section('content')
<div class="card border-0 shadow rounded">
    <h5 class="card-header">DOCUMENTATION BOSSTRAP COMPONENT</h5>
    <div class="card-body">
<?php
use \fidpro\builder\Bootstrap;
$hasil = Bootstrap::tabs([
    "tabs"  => [
        "home"  => [
            "href"      => "home",
            "content"   => "<h1>ini home</h1>"
        ],
        "profil"  => [
            "href"      => "profil",
            "content"   => "<h1>ini profil</h1>"
        ],
        "load"  => [
            "href"      => "loadpage",
            "url"       => "builder/example/bootsrap_component/load_tab_page"
        ]
    ]
]);
print_r ($hasil);
?>
<button onclick="$('#modal_example').modal('show')" class="btn btn-primary">show modal</button>
{{
    Bootstrap::modal('modal_example',[
        "title"   => 'Download data tindakan',
        "size"    => "modal-sm",
        "body"    => [
                        "content"   => "ini content"
                    ]
    ])
}}
    </div>
</div>
@endsection