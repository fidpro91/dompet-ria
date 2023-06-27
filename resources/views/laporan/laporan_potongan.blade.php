<?php
use \fidpro\builder\Create;
use \fidpro\builder\Widget;
Widget::_init(["select2"]);
?>
{!! Form::open(['url' => 'laporan/laporan_potongan','id'=>'laporan_potongan','target'=>'_blank']) !!}
<div class="card-body">
    {!! 
        Widget::select2("id_cair2",[
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
        Widget::select2("kategori_id",[
            "data" => [
                "model"     => "Kategori_potongan",
                "filter"    => ["potongan_type" => "2"],
                "column"    => ["kategori_potongan_id","nama_kategori"]
            ]
        ])->render("group","Kategori Potongan");
    !!}
</div>
<div class="card-footer text-center">
    {!! Form::submit('Cetak',['class' => 'btn btn-primary']); !!}
    {!! Form::submit('Excel',['class' => 'btn btn-success']); !!}
</div>
{!!Form::close()!!}