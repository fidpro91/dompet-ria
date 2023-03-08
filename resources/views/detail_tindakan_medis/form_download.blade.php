<?php
use \fidpro\builder\Create;
use \fidpro\builder\Widget;
Widget::_init(["select2","datepicker","daterangepicker"]);
?>
<div class="row">
    <div class="col-xl-12 col-md-6">
        {!!Form::open(["url" => "detail_tindakan_medis/get_data_simrs","id"=>"form_download"])!!}
        {!!Widget::datepicker("bulan_jasa",[
        "format" =>"mm-yyyy",
        "viewMode" => "year",
        "minViewMode" => "year",
        "autoclose" =>true
        ],[
        "readonly" => true,
        "value" => date('m-Y')
        ])->render("group","Bulan Pembagian Jasa")!!}
        {!!Widget::datepicker("bulan_pelayanan",[
        "format" =>"mm-yyyy",
        "viewMode" => "year",
        "minViewMode" => "year",
        "autoclose" =>true
        ],[
        "readonly" => true,
        "value" => date('m-Y')
        ])->render("group")!!}
        {!!Widget::daterangePicker("periode_tindakan")->render("group")!!}
        {!!
            Widget::select2("surety_id",[
                "data" => [
                    "model"     => "Ms_reff",
                    "filter"    => ["reffcat_id" => "5"],
                    "column"    => ["reff_code","reff_name"]
                ],
                "extra" => [
                    "name"      => "surety_id[]",
                    "multiple"  => "true"
                ]
            ])->render("group","Penjamin")
        !!}
        {!!
            Create::dropDown("jenis_pembayaran",[
            "data" => [
                ["1" => "Tunai"],
                ["2" => "Piutang"]
            ]
        ])->render("group")!!}
        <button class="btn btn-block btn-success btn-download">Download</button>
        {!!Form::close()!!}
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
                title: 'Download data tindakan medis?',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
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
                        'type'    : 'post',
                        'url'     : '{{URL("detail_tindakan_medis/get_data_simrs")}}',
                        'success': function(data) {
                            if (data.success) {
                                Swal.fire("Sukses!", data.message, "success").then(() => {
                                    location.href= "{{URL('detail_tindakan_medis/download_page')}}";
                                });
                            }else{
                                Swal.fire("Oopss..!!", data.message, "error")
                            }
                        }
                    });
                }
            })
            return false;
        });
    })
</script>