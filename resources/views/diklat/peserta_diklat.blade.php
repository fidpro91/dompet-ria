@extends('templates.layoutNoHeader')
@section('content')
<?php

use \fidpro\builder\Bootstrap;
use \fidpro\builder\Create;
use \fidpro\builder\Widget;

Widget::_init(["select2"]);
?>
<div class="card border-0 shadow rounded" id="page_diklat">
{!! Form::open(['url' => 'pengajuan_diklat/find','id'=>'form_diklat','enctype'=>'multipart/form-data']) !!}
<div class="card-body">
    {!! 
        Widget::select2("peserta_id",[
            "data" => [
                "model"     => "Employee",
                "filter"    => ["emp_active" => "t"],
                "column"    => ["emp_id","emp_name"]
            ]
        ])->render("group","Nama Peserta")
    !!}
    {!! 
        Create::input("nomor_rekening",[
            "required" => "true"
        ])->render("group");
    !!}
</div>
<div class="card-footer text-center">
    {!! Form::submit('Cari Data',['class' => 'btn btn-success']); !!}
    {!! Form::button('Cancel',['class' => 'btn btn-warning btn-refresh']); !!}
</div>
{!!Form::close()!!}
</div>
@endsection