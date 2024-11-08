
<?php
use \fidpro\builder\Bootstrap;
use fidpro\builder\Widget;
Widget::_init(["datepicker"]);
?>
<div class="card border-0 shadow rounded" id="page_rekap_ijin">

    <div class="card-header">
        <div class="row">
            <div class="col-md-8">
            {!!
                    Form::button("Tambah Manual",[
                        "class" => "btn btn-primary add-form",
                        "data-target" => "page_rekap_ijin",
                        "data-url" => route("rekap_ijin.create")
                    ])
            !!}
                {!!
                    Form::button("Generate ijin Prestige",[
                        "class" => "btn btn-purple",                        
                        "id" => "btn-rekapIjin"
                    ])
                !!}
            </div>
            <div class="col-sm-4">
                    {!! 
                        Widget::datepicker("tahun_filter",[
                            "format"		=>"yyyy",
                            "viewMode"		=> "years",
                            "minViewMode"	=> "years",
                            "autoclose"		=> true
                        ],[
                            "readonly"      => true,
                            "value"         => date('Y')
                        ])->render()
                    !!}
                </div> 
        </div> 
    </div>
    
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table_rekap_izin",[
                    "class" => "table table-hover"
                ],[
                    "url"   => "rekap_ijin/get_dataTable",
                    "filter" => ["tahun_update" => "$('#tahun_filter').val()"],
                    "raw"   => [
                        '#'     => [
                            "data" => "action", 
                            "name" => "action", 
                            "orderable" => "false", 
                            "searchable" => "false"
                        ],
                        'no'    => [
                            "data" => "DT_RowIndex",
                            "orderable" => "false", 
                            "searchable" => "false"
                        ],
                        'nip','nama_pegawai','jenis_ijin','tipe_ijin','tgl_mulai','tgl_selesai','lama_ijin','keterangan'
                    ]
                ])
            }}
        </div>
    </div>
</div>
{{
    Bootstrap::modal('modal_rekapIjin', [
        "title" => 'Form Update Rekap Ijin Pegawai',
        "size" => "modal-md",
        "body" => [
            "content"   => function(){
                return view('rekap_ijin.modal_rekap_ijin');
            }
        ]
    ])
}}

<script>
$(document).ready(()=>{
            $("#btn-rekapIjin").click(()=>{
            $("#modal_rekapIjin").modal("show");
           
        })

        $("#tahun_filter").change(()=>{
            tb_table_rekap_izin.draw();
        });
    })
</script>