@extends('templates.layout')
@section('content')
<?php

use fidpro\builder\Widget;

Widget::_init(["select2"]);
?>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                {!! 
                    Widget::select2("employee_edit",[
                        "data" => [
                            "model"     => "Employee",
                            "column"    => ["emp_id","emp_name"]
                        ]
                    ])->render("group","Nama Pegawai");
                !!}
            </div>
            <div class="col-md-2">
                <button class="btn btn-info btn-block" id="tampil">Tampilkan</button>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body" id="form-update">
    </div>
</div>
<script>
    $(document).ready(()=>{
        $("#tampil").click(()=>{
            let emp_id = $("#employee_edit").val();
            let url = emp_id+"/edit";
            $("#form-update").html('');
            
            $.ajaxSetup({
                "type": "post",
                "url": "update_data",
                headers: {
                'X-CSRF-TOKEN': "{{csrf_token()}}"
                }
            });

            $.get(url,function(resp){
                $("#form-update").html(resp);
            })
        })
    })
</script>
@endsection