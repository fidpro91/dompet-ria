<?php
use \fidpro\builder\Widget;
use \fidpro\builder\Bootstrap;
use fidpro\builder\Create;
Widget::_init(["select2","datepicker","inputmask"]);
?>
{!!
    Form::open(["id"=>"formHitung"])
!!}
<div class="row">
    <div class="col-md-7">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table_tindakan",[
                    "class" => "table table-hover"
                ],[
                    "url"       => "repository_download/get_dataTable",
                    "filter"    => ["is_used" => "'f'"],
                    "raw"       => [
                        'id'  => [
                            "data"      => "id",
                            "name"      => "id",
                            "settings"  => [
                                "render"  => "function (data, type, full, meta){
                                            return '<input required type=\"checkbox\" name=\"repo_id[]\" value=\"' + $('<div/>').text(data).html() + '\">';
                                }"
                            ]
                        ],
                        'no' => [
                            "data" => "DT_RowIndex",
                            "orderable" => "false", 
                            "searchable" => "false"
                        ],'download_date','download_no','group_penjamin'
                    ],
                    "dataTable"  => [
                        "paging"    => "false",
                        "ordering"  => "false",
                        "info"      => "false",
                    ]
                ])
            }}
        </div>
    </div>
    <div class="col-md-5">
        <div class="row">
            <div class="col-md-6">
                {!!
                    Widget::datepicker("tanggal_jaspel",[
                        "autoclose"		=> true
                    ],[
                        "readonly"      => true,
                        "required"      => true,
                        "value"         => date('d-m-Y')
                    ])->render("group")
                !!}
                {!! 
                    Widget::inputMask("nominal_pendapatan",[
                        "prop"      => [
                            "required"  => true,
                        ],
                        "mask"      => [
                            "IDR",[
                                "rightAlign"    => false,
                            ]
                        ]
                    ])->render("group");
                !!}
            </div>
            <div class="col-md-6">
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
                {!! 
                    Widget::inputMask("percentase_jaspel",[
                        "prop"      => [
                            "required"  => true,
                        ],
                        "mask"      => [
                            "IDR",[
                                "rightAlign"    => false,
                            ]
                        ]
                    ])->render("group");
                !!}
            </div>
            <div class="col-md-12">
                {!! 
                    Widget::inputMask("nominal_pembagian",[
                        "prop"      => [
                            "required"  => true,
                            "readonly"  => true,
                        ],
                        "mask"      => [
                            "IDR",[
                                "rightAlign"    => false,
                            ]
                        ]
                    ])->render("group","Nilai Awal Estimasi Pembagian Jasa");
                !!}
                {!!
                    Create::text("keterangan")->render("group");
                !!}
            </div>
        </div>
        {!! Form::button('Hitung Jasa Pelayanan',['class' => 'btn btn-block btn-success','type' => 'submit']); !!}
        
    </div>
</div>
{!!
    Form::close()
!!}
<script>
    $(document).ready(()=>{
        $('#formHitung').parsley().on('field:validated', function() {
            var ok = $('.parsley-error').length === 0;
            $('.bs-callout-info').toggleClass('hidden', !ok);
            $('.bs-callout-warning').toggleClass('hidden', ok);
        })
        .on('form:submit', function() {
            Swal.fire({
                title: 'Simpan Data?',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        'data': $('#formHitung').serialize(),
                        'dataType': 'json',
                        'type'  : 'post',
                        'headers': {
                            'X-CSRF-TOKEN': "<?= csrf_token() ?>"
                        },
                        'beforeSend': function() {
                            showLoading();
                        },
                        'url'   : '{{url("jasa_pelayanan/hitung_jasa")}}',
                        'success': function(data) {
                            if (data.success) {
                                Swal.fire("Sukses!", data.message, "success").then(() => {
                                    location.href = "{{url('jasa_pelayanan/hasil_hitung_sementara')}}";
                                });
                            }else{
                                Swal.fire("Oopss...!!", data.message, "error");
                            }
                        }
                    });
                }
                return false;
            })
        })

    })
    $("#percentase_jaspel, #nominal_pendapatan").on("change",()=>{
        hitung_jasa();
    })

    function hitung_jasa() {
        let percentase = parseFloat($.isNumeric($('#percentase_jaspel').val())?$('#percentase_jaspel').val():0);
		let pendapatan = parseFloat($.isNumeric($("#nominal_pendapatan").val())?$("#nominal_pendapatan").val():0);
		let hasil = percentase/100*pendapatan;
		$("#nominal_pembagian").val(hasil);
    }
</script>