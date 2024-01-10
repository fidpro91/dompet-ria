@extends('templates.layoutNoHeader')
@section('content')
<?php

use \fidpro\builder\Bootstrap;
use \fidpro\builder\Create;
use \fidpro\builder\Widget;

Widget::_init(["daterangepicker"])
?>
<div class="bg-picture card-box">
    <div class="profile-info-name">
        <img src="{{asset('assets/images/no-photo.jpeg')}}" class="rounded-circle avatar-xl img-thumbnail float-left mr-3" alt="profile-image">

        <div class="profile-info-detail overflow-hidden">
            <h4 class="m-0">{{strtoupper(session('peserta')->emp_name)}}</h4>
            <p class="text-muted"><i>{{session('peserta')->unit_name}}</i></p>
            <p class="font-13">NIP : {{session('peserta')->emp_no}}</p>
            <p class="font-13">Golongan : {{session('peserta')->golongan}}</p>
            <p class="font-13">Unit Kerja : {{session('peserta')->unit_name}}</p>
        </div>

        <div class="clearfix"></div>
    </div>
</div>
<div class="card border-0 shadow rounded" id="page_diklat">
{!! Form::open(['url' => 'pengajuan_diklat/store','id'=>'form_diklat','enctype'=>'multipart/form-data']) !!}
<div class="card-body">
    {!! 
        Create::input("judul_pelatihan",[
            "required" => "true"
        ])->render("group");
    !!}
    {!!
        Widget::daterangePicker("tanggal_pelatihan")->render("group")    
    !!}
    {!! 
        Create::input("penyelenggara",[
            "required" => "true"
        ])->render("group");
    !!}
    {!! 
        Create::input("lokasi_pelatihan")->render("group");
    !!}
    {!! 
        Create::input("sertifikat_no",[
            "required" => "true"
        ])->render("group");
    !!}
    {!! 
        Create::upload("sertifikat_file")->render("group");
    !!}
</div>
<div class="card-footer text-center">
    {!! Form::submit('Save',['class' => 'btn btn-success']); !!}
    {!! Form::button('Cancel',['class' => 'btn btn-warning btn-refresh']); !!}
</div>
{!!Form::close()!!}
</div>
@endsection