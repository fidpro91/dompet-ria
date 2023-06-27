<?php
use \fidpro\builder\Bootstrap;
use \fidpro\builder\Create;
use \fidpro\builder\Widget;

Widget::_init(["datepicker"]);
?>
{!! Form::open(['route' => 'pencairan_jasa_header.store','id'=>'form_pencairan_jasa_header']) !!}
<div class="row">
    <div class="col-md-8">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table_brutto",[
                    "class" => "table table-hover"
                ],[
                    "url"       => "jasa_pelayanan/get_dataTable",
                    "filter"    => ["status" => '1'],
                    "raw"   => [
                        '<input type="checkbox" name="check-all-jaspel" class="check-all"/>'     => [
                            "data"      => "jaspel_id", 
                            "name"      => "action",
                            "settings"  => [
                                "render"    => "function (data, type, full, meta){
                                                    return '<input required type=\"checkbox\" name=\"jaspel_id[]\" class=\"jaspel_id\" value=\"' + $('<div/>').text(data).html() + '\">';
                                                }",
                                "orderable" => "false", 
                                "searchable" => "false"
                            ]
                        ],
                        'no_jasa','penjamin',
                        'nominal_jaspel'     => [
                            "data"      => "nominal_jaspel",
                            "settings"  => [
                                "render"    => "$.fn.dataTable.render.number( ',', '.', 2)"
                            ]
                        ],
                    ]
                ])
            }}
        </div>
    </div>
    <div class="col-md-4">
        {!! Create::input("no_pencairan",[
            "value"     => $nomor,
            "readonly"  => "true"
        ])->render("group"); 
        !!}
        {!! 
            Widget::datepicker("tanggal_cair",[
                "format"		=>"dd-mm-yyyy",
                "autoclose"		=>true
            ],[
                "readonly"      => true,
                "required"      => "true"
            ])->render("group")
        !!}
        {!!
            Create::text("keterangan")->render("group");
        !!}
        {!! Form::submit('Prosess Pencairan Jasa',['class' => 'btn btn-success btn-block']); !!}
    </div>
</div>
{!!Form::close()!!}
<script>
    $(document).ready(()=>{
        $('body').find(".CHECK-ALL").on('click', (function() {
            if ($(this).is(":checked")) {
                $(this).closest("table").find("input[type='checkbox']").attr('checked', true);
            } else {
                $(this).closest("table").find("input[type='checkbox']").attr('checked', false);
            }
        }));
        $('#form_pencairan_jasa_header').parsley().on('field:validated', function() {
            var ok = $('.parsley-error').length === 0;
            $('.bs-callout-info').toggleClass('hidden', !ok);
            $('.bs-callout-warning').toggleClass('hidden', ok);
        })
        .on('form:submit', function() {
            if ($(".jaspel_id:checked").length == 0) {
                Swal.fire("Mohon dicentang list perhitungan jasa","", "error");
                return false;
            }
            Swal.fire({
                title: 'Proses pencairan jasa?',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        'data': $('#form_pencairan_jasa_header').serialize(),
                        'dataType': 'json',
                        'type'  : 'post',
                        'headers': {
                            'X-CSRF-TOKEN': "<?= csrf_token() ?>"
                        },
                        'beforeSend': function() {
                            showLoading();
                        },
                        'url'   : '{{route("pencairan_jasa_header.store")}}',
                        'success': function(data) {
                            if (data.success) {
                                Swal.fire("Sukses!", data.message, "success").then(() => {
                                    location.reload();
                                    // location.href = "{{url('jasa_pelayanan/hasil_hitung_sementara')}}";
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