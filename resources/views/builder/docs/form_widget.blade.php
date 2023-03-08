@extends('builder.template.index')
@section('content')
<div class="card border-0 shadow rounded">
    <h5 class="card-header">DOCUMENTATION FORM WIDGET</h5>
    <div class="card-body">
<?php
use \fidpro\builder\Widget;
Widget::_init(["select2"]);
$hasil = Widget::select2("select2pegawai",[
    "data" => [
        "model"     => "Employee",
        "custom"    => "tes_data",
        "column"    => ["emp_id","emp_name"]
    ]
])->render("group","Pegawai Select2");
print_r ($hasil);
$hasil = Widget::inputMask("gaji_pokok",[
    "prop"      => [
        "value"     => $employee->gaji_pokok,
        "required"  => true,
    ],
    "mask"      => [
        "IDR",[
            "rightAlign"    => false,
        ]
    ]
])->render("group");;
print_r ($hasil);
?>

    </div>
</div>
@endsection