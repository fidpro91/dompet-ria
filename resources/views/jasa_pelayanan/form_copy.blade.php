<?php
use \fidpro\builder\Widget;

Widget::_init(["select2","datepicker"]);
?>
<div class="row">
    <div class="col-md-12">
        {!!
            Form::open(["id"=>"formCopy"])
        !!}
        {!!
            Widget::select2("jasa_pelayanan",[
                "data" => [
                    "model"     => "Proporsi_jasa_individu",
                    "custom"    => "get_jaspel",
                    "column"    => ["id_jaspel","no_jaspel"]
                ],
                "extra" => [
                    "required"  => true
                ]
            ])->render("group")
        !!}
        {!!
            Widget::select2("komponen_id2",[
                "data" => [
                    "model"     => "Komponen_jasa",
                    "filter"    => ["has_child" => "f"],
                    "column"    => ["komponen_id","komponen_nama"]
                ]
            ])->render("group","Proporsi Jasa")
        !!}
        {!!
            Widget::datepicker("bulan_skor",[
                "format"		=>"mm-yyyy",
                "viewMode"		=> "year",
                "minViewMode"	=> "year",
                "autoclose"		=>true
            ],[
                "readonly"      => true,
                "required"  => true,
                "value"         => date('m-Y')
            ])->render("group","Copy Untuk Jasa Bulan")
        !!}
        {!! Form::button('Copy Proporsi Jasa',['class' => 'btn btn-block btn-success btn-download','type' => 'submit']); !!}
        {!!
            Form::close()
        !!}
    </div>
</div>
<script>
    $('#formCopy').parsley().on('field:validated', function() {
            var ok = $('.parsley-error').length === 0;
            $('.bs-callout-info').toggleClass('hidden', !ok);
            $('.bs-callout-warning').toggleClass('hidden', ok);
        })
        .on('form:submit', function() {
        $.ajax({
            'data': $('#formCopy').serialize(),
            'dataType': 'json',
            'headers': {
                'X-CSRF-TOKEN': "<?= csrf_token() ?>"
            },
            'type':'post',
            'url' : '{{url("proporsi_jasa_individu/copy_data")}}',
            'beforeSend': function() {
                var showLoading = function() {
                    Swal.fire({
                        html: 'Mohon tunggu...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                    });
                }
                showLoading();
            },
            'success': function(data) {
                if (data.success) {
                    Swal.fire("Sukses!", data.message, "success").then(() => {
                        $("#modal_copy").modal("hide");
                        tb_table_data_proporsi.draw();
                        tb_table_employee.draw();
                    });
                }else{
                    Swal.fire("Oopss...!!", data.message, "error");
                }
            }
        });
        return false;
    })
</script>