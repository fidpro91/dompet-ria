<?php
use \fidpro\builder\Create;
?>
<div class="row">
    <div class="col-md-12">
        {!!
            Form::open(["id"=>"formPrinter", "url" => "jasa_pelayanan/cetak" , "method" => "get", "target" => "blank"])
        !!}
        {!! Form::hidden('jaspel_id','', array('id' => 'jaspel_id')) !!}
        {!! 
            Create::dropDown("jenis_report",[
                "data" => [
                    ["1"     => "Rekap Detail"],
                    ["2"     => "Struktural"],
                    ["3"     => "Medis"]
                ]
            ])->render("group","Jenis Laporan");
        !!}
        {!! 
            Form::button('Cetak Laporan',['class' => 'btn btn-block btn-secondary btn-cetak','type' => 'submit']); 
        !!}
        {!!
            Form::close()
        !!}
    </div>
</div>