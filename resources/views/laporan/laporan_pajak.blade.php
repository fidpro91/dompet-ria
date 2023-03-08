<?php
use \fidpro\builder\Create;
use \fidpro\builder\Widget;
Widget::_init(["select2"]);
?>
    {!! Form::open(['url' => 'laporan/laporan_pajak','id'=>'laporan_pajak','target'=>'_blank']) !!}
    <div class="card-body">
        {!! 
            Widget::select2("id_cair",[
                "data" => [
                    "model"     => "Pencairan_jasa_header",
                    "filter"    => ["is_published" => "1"],
                    "column"    => ["id_cair_header","no_pencairan"]
                ],
                "extra"     => [
                    "required"  => true
                ]
            ])->render("group","Nomor Pencairan Jasa");
        !!}
        {!! 
            Create::dropDown("emp_status",[
                "data" => [
                    ["1"     => "PNS"],
                    ["2"     => "BLUD"]
                ],
                "extra"     => [
                    "required"  => true
                ]
            ])->render("group","Jenis Pegawai");
        !!}
        {!! 
            Create::dropDown("is_medis",[
                "data" => [
                    ["t"     => "Ya"],
                    ["f"     => "Tidak"]
                ],
                "extra"     => [
                    "required"  => true
                ]
            ])->render("group","Pegawai Medis");
        !!}
    </div>
    <div class="card-footer text-center">
        {!! Form::submit('Cetak',['class' => 'btn btn-primary']); !!}
        {!! Form::submit('Excel',['class' => 'btn btn-success']); !!}
    </div>
    {!!Form::close()!!}