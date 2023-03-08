@extends('builder.template.index')
@section('content')
<div class="card border-0 shadow rounded">
    <h5 class="card-header">DOCUMENTATION FORM ELEMENT GENERAL</h5>
    <div class="card-body">
<?php
use \fidpro\builder\Create;
use \fidpro\builder\Widget;
Widget::_init(["select2"]);
$hasil = Create::input("nama_pegawai")->render();
echo ($hasil);
$hasil = Create::upload("file_pegawai")->render('group');
echo ($hasil);
$hasil = Create::dropDown("pegawai",[
    "data" => [
        "model"     => "Models_builder\Employee",
        "custom"    => "tes_data",
        "column"    => ["emp_id","emp_name"]
    ]
])->render("group","pegawai");
print_r ($hasil);
$hasil = Create::dropDown("pegawai",[
    "data" => [
        "t"     => "Aktif",
        "f"     => "Non Aktif"
    ]
])->render("group","pegawai");
print_r ($hasil);
$hasil = Create::radio("pegawai",[
    "data" => [
        "t"     => "Aktif",
        "f"     => "Non Aktif"
    ]
])->render("group","pegawai");
print_r ($hasil);
$hasil = Create::checkbox("pegawai",[
    "data" => [
        "t"     => "Aktif",
        "f"     => "Non Aktif"
    ]
])->render("group","pegawai");
print_r ($hasil);
$hasil = Create::radio("radio_pegawai",[
    "data" => [
        "model"     => "Models_builder\Employee",
        "custom"    => "tes_data",
        "column"    => ["emp_id","emp_name"]
    ]
])->render("group","pegawai");
print_r ($hasil);
$hasil = Create::checkbox("check_pegawai",[
    "data" => [
        "model"     => "Models_builder\Employee",
        "custom"    => "tes_data",
        "column"    => ["emp_id","emp_name"]
    ]
])->render("group","pegawai");
print_r ($hasil);
?>
    </div>
</div>
{!! Create::dropDown("status",[
    "data" => [
    "t" => "Aktif",
    "f" => "Non Aktif"
    ]
    ])->render("group")
    !!}
@endsection