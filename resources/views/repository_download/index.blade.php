<?php
use \fidpro\builder\Bootstrap;
use \fidpro\builder\Widget;

Widget::_init(["select2"]);
?>
<div class="card border-0 shadow rounded" id="page_repository_download">
    <div class="card-body">
        <div class="row">
            <div class="col-3">
                {!!
                    Widget::select2("filter_penjamin",[
                        "data" => [
                            "model"     => "Ms_reff",
                            "filter"    => [
                                "reffcat_id"  => "5"
                            ],
                            "column"    => ["reff_code","reff_name"]
                        ]
                    ])->render("group","Penjamin")
                !!}
            </div>
            <div class="col-3">
                {!!
                    Widget::datepicker("filter_bulan",[
                        "format"		=>"mm-yyyy",
                        "viewMode"		=> "year",
                        "minViewMode"	=> "year",
                        "autoclose"		=> true,
                        "clearBtn"      => true
                    ],[
                        "readonly"      => true
                    ])->render("group","Bulan Pelayanan")
                !!}
            </div>
        </div>
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table-data",[
                    "class" => "table table-hover"
                ],[
                    "url"   => "repository_download/get_dataTable",
                    "filter"    => [
                        "penjamin_id"       => '$("#filter_penjamin").val()',
                        "bulan_pelayanan"   => '$("#filter_bulan").val()'
                    ],
                    "raw"   => [
                        '#'     => [
                            "data"      => "action", 
                            "name"      => "action",
                            "settings"  => [
                                "orderable" => "false", 
                                "searchable" => "false"
                            ]
                        ],
                        'no'    => [
                            "data" => "DT_RowIndex",
                            "settings"  => [
                                "orderable" => "false", 
                                "searchable" => "false"
                            ]
                        ],
                        'download_no',
                        'jml_jaspel','bulan_pelayanan','periode_awal','group_penjamin','jenis_pembayaran','total_data'
                    ],
                    "dataTable"    => [
                        "order"    => "[[2,'desc']]"
                    ]
                ])
            }}
        </div>
    </div>
</div>
{{
    Bootstrap::modal('modal_copy',[
        "title"   => 'Copy data tindakan medis',
        "size"    => "modal-md",
        "body"    => [
            "content"   => function (){
                return view("repository_download.form_copy");
            }
        ]
    ])
}}
{{
    Bootstrap::modal('modal_detail',[
        "title"   => 'List Data Download Tindakan Pelayanan',
        "size"    => "modal-xl",
        "body"    => [
            "url"   => url("detail_tindakan_medis/data_tindakan")
        ]
    ])
}}
<script>
    var repoId;
    function copy_data(id) {
        $("#modal_copy").modal("show");
        $("#modal_copy").find("#id").val(id);
    }

    function show_data(id) {
        repoId=id;
        $("#modal_detail").modal("show");
    }

    function delete_copy(id) {
        Swal.fire({
                title: 'Hapus data medis yang tercopy?',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.value) {
                    showLoading();
                    $.get("{{url('repository_download/delete_copy')}}/"+id,function(data){
                        if (data.success) {
                            Swal.fire("Sukses!", data.message, "success");
                        }else{
                            Swal.fire("Oopss...!!", data.message, "error");
                        }
                    },'json');
                }
            })
    }

    $(document).ready(()=>{
        $("#filter_bulan, #filter_penjamin").on("change",function(){
            tb_table_data.draw();
        });
    })
</script>