
<?php
use \fidpro\builder\Bootstrap;
use fidpro\builder\Widget;
use \fidpro\builder\Create;
Widget::_init(["datepicker","daterangepicker"]);
?>
<div class="card border-0 shadow rounded" id="page_rekap_ijin">

    <div class="card-header">
        <div class="row">
            <div class="col-md-8">
           
                {!!
                    Form::button("Generate ijin Prestige",[
                        "class" => "btn btn-purple",                        
                        "id" => "btn-rekapIjin"
                    ])
                !!}
                {!!
                    Form::button("Insert ke Pegawai off",[
                        "class" => "btn btn-warning",                        
                        "id" => "btn-insert"
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
            <div class="form-group col-md-6">
                <label for="lama_izin" class="col-md-3 col-form-label">Lama Izin</label>
                <div class="col-md-9">
                    <select class="form-control" id="lama_izin">
                        <option value="">SEMUA</option>
                        <option value="1">KURANG DARI 3 HARI</option>
                        <option value="2">LEBIH DARI 3 HARI</option>
                    </select>
                </div>
        </div>
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table_rekap_izin",[
                    "class" => "table table-hover"
                ],[
                    "url"   => "rekap_ijin/get_dataTable",
                    "filter" => ["tahun_update" => "$('#tahun_filter').val()",
                                 "periode_awal" => "$('#periode_awal').val()",
                                 "lama" => "$('#lama_izin').val()",                                
                                 ],
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
                    ],
                    "dataTable" => [
                        "order" => "[[3,'ASC']]"
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
{{
    Bootstrap::modal('modal_insert', [
        "title" => 'Form Insert Ke Pegawai of Jaspel',
        "size" => "modal-xl",
        "body" => [
            "content"   => function(){
                return view('rekap_ijin.modal_insert_of_jaspel');
            }
        ]
    ])
}}

<script>
$(document).ready(()=>{
            $("#btn-rekapIjin").click(()=>{
            $("#modal_rekapIjin").modal("show");           
        })
        $("#btn-insert").click(()=>{
            $("#modal_insert").modal("show");           
        })

        $("#lama_izin").change(()=>{
            tb_table_rekap_izin.draw();
        });
    })
</script>