<?php
use \fidpro\builder\Widget;
Widget::_init(["daterangepicker"]);
?>
<div class="row">
    <div class="col-xl-12 col-md-6">
        {!! Form::open(["url" => "detail_tindakan_medis/kroscek_tindakan","id"=>"form_kroscek"]) !!}
        {!! Widget::daterangePicker("periode_tindakan_kroscek")->render("group","Periode Tarikan Data") !!}
        {!! Form::submit('Check Tindakan Medis',['class' => 'btn btn-block btn-success btn-check']); !!}
        {!!
            Form::close()
        !!}
    </div>
</div>