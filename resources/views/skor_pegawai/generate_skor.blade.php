<?php
use \fidpro\builder\Create;
use \fidpro\builder\Widget;

Widget::_init(["select2","datepicker"]);
?>
<div class="row">
    <div class="col-md-12">
        {!!
            Form::open(["url" => "detail_tindakan_medis/get_data_simrs","id"=>"form_download"])
        !!}
        {!!
            Widget::datepicker("bulan_skor",[
                "format"		=>"mm-yyyy",
                "viewMode"		=> "year",
                "minViewMode"	=> "year",
                "autoclose"		=>true
            ],[
                "readonly"      => true,
                "value"         => date('m-Y')
            ])->render("group","Bulan Pembuatan Skor")
        !!}
        {!!
            Widget::select2("unit_id",[
                "data" => [
                    "model"     => "Ms_unit",
                    "filter"    => ["is_active"  => "t"],
                    "column"    => ["unit_id","unit_name"]
                ]
            ])->render("group","Generate Berdasarkan Unit Kerja")
        !!}
        {!!
            Widget::select2("emp_id_gen",[
                "data" => [
                    "model"     => "Employee",
                    "filter"    => ["emp_active" => "t"],
                    "column"    => ["emp_id","emp_name"]
                ]
            ])->render("group","Generate Berdasarkan Pegawai")
        !!}
        {!! Form::button('Generate Skor Pegawai',['class' => 'btn btn-block btn-success btn-download','type' => 'submit']); !!}
        {!!
            Form::close()
        !!}
    </div>
</div>
<script>
    $(document).ready(()=>{
        $('#form_download').parsley().on('field:validated', function() {
            var ok = $('.parsley-error').length === 0;
            $('.bs-callout-info').toggleClass('hidden', !ok);
            $('.bs-callout-warning').toggleClass('hidden', ok);
        })
        .on('form:submit', function() {
            Swal.fire({
                title: 'Generate Skor Individu Pegawai?',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        'data': $('#form_download').serialize(),
                        'dataType': 'json',
                        'headers': {
                            'X-CSRF-TOKEN': "<?=csrf_token()?>"
                        },
                        'beforeSend': function() {
                            showLoading();
                        },
                        'type'   : 'post',
                        'url'   : '{{url("skor_pegawai/generate_skor")}}',
                        'success': function(data) {
                            if (data.success) {
                                Swal.fire("Sukses!", data.message, "success").then(() => {
                                    location.href = "{{url('skor_pegawai/hasil_skor')}}";
                                });
                            }else{
                                Swal.fire("Oopss...!!", data.message, "error");
                            }
                        }
                    });
                }
            })
            return false;
        });
    })
</script>