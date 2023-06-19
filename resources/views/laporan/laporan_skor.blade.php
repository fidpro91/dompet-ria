@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Widget;
Widget::_init(["datepicker","select2"]);
?>
<div class="row">
    
    <div class="col-md-5">
        <div class="card card-inverse text-white">
            <img class="card-img img-fluid" src="{{asset('assets/images/icon-system/reporting.gif')}}" alt="Card image">
        </div>
    </div>
    <div class="col-md-7">
        <div class="card border-0 shadow rounded">
        {!! Form::open(['url' => 'laporan/show_skor_pegawai','id'=>'rekap_jaspel','target'=>'_blank']) !!}
            <div class="card-body">
                {!!
                    Widget::datepicker("bulan_skor",[
                        "format"		=>"mm-yyyy",
                        "viewMode"		=> "year",
                        "minViewMode"	=> "year",
                        "autoclose"		=> true,
                        "orientation"   => "bottom"
                    ],[
                        "readonly"      => true,
                        "required"  => true,
                        "value"         => date('m-Y')
                    ])->render("group","Bulan Pembuatan Skor")
                !!}
                {!!
                    Widget::select2("unit_id",[
                        "data" => [
                            "model"     => "Ms_unit",
                            "filter"    => ["is_active"  => "t"],
                            "column"    => ["unit_id","unit_name"]
                        ],
                        "extra" => [
                            "name"      => "unit_id[]",
                            "multiple"  => "true"
                        ]
                    ])->render("group","Unit Kerja")
                !!}
            </div>
            <div class="card-footer text-center">
                {!! Form::submit('Cetak',['class' => 'btn btn-primary']); !!}
                {!! Form::submit('Excel',['class' => 'btn btn-success']); !!}
            </div>
        {!!Form::close()!!}
        </div>
    </div>
</div>
@endsection