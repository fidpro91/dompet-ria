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
        {!! Form::open(['url' => 'rekap_jaspel/detail','id'=>'rekap_jaspel','target'=>'_blank']) !!}
            <div class="card-body">
                {!!
                    Widget::datepicker("jaspel_bulan",[
                        "format"		=>"mm-yyyy",
                        "viewMode"		=> "year",
                        "minViewMode"	=> "year",
                        "autoclose"		=>true
                    ],[
                        "readonly"      => true,
                        "required"  => true,
                        "value"         => date('m-Y')
                    ])->render("group","Bulan Pembagian Jaspel")
                !!}
                @if(in_array(Session::get('sesLogin')->group_type, [1, 8]))
                {!! 
                    Widget::select2("emp_id",[
                        "data" => [
                            "model"     => "Employee",
                            "filter"    => ["emp_active" => "t"],
                            "column"    => ["emp_id","emp_name"]
                        ]
                    ])->render("group","Nama Pegawai");
                !!}
                @endif
            </div>
            <div class="card-footer text-center">
                {!! Form::submit('Download Rincian',['class' => 'btn btn-warning btn-block']); !!}
            </div>
        {!!Form::close()!!}
        </div>
    </div>
</div>
@endsection