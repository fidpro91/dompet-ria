
<?php
use \fidpro\builder\Bootstrap;
use fidpro\builder\Widget;
Widget::_init(["datepicker"]);

?>
<div class="card border-0 shadow rounded" id="page_table_rekap_absen">
    <div class="card-header">
         <div class="row">
            <div class="col-md-9">
                {!!
                    Form::button("Tambah Manual",[
                        "class" => "btn btn-primary add-form",
                        "data-target" => "page_table_rekap_absen",
                        "data-url" => route("table_rekap_absen.create")
                    ])
                !!}
                {!!
                    Form::button("Get Data Prestige",[
                        "class" => "btn btn-purple",
                        "data-target" => "page_table_rekap_absen", 
                        "id" => "btn-prestige",               
                        "data-url" => route("table_rekap_absen.create")
                    ])
                !!}
                {!!
                    Form::button("Insert Kedisiplinan",[
                        "class" => "btn btn-warning",
                        "data-target" => "page_table_rekap_absen",
                        "id" => "btn-disiplin",
                        "data-url" => route("table_rekap_absen.create")
                    ])
                !!} 
            </div>
            <div class="col-sm-3">
                    {!! 
                        Widget::datepicker("filter_bulan",[
                            "format"		=>"mm-yyyy",
                            "viewMode"		=> "year",
                            "minViewMode"	=> "year",
                            "autoclose"		=> true
                        ],[
                            "readonly"      => true,
                            "value"         => date('m-Y')
                        ])->render()
                    !!}
                </div>   
         </div>    
    </div>
   
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table_prestige",[
                    "class" => "table table-hover"
                ],[
                    "url"   => "table_rekap_absen/get_dataTable",
                    "filter"    => ["bulan_prestige" => "$('#filter_bulan').val()"],
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
                        'nip','nama_pegawai','bulan_update','tahun_update','persentase_kehadiran','keterangan'
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
    Bootstrap::modal('modal_bridging', [
        "title" => 'Update SKOR By Bridging',
        "size" => "modal-md",
        "body" => [
            "content"   => function(){
                return view('table_rekap_absen.modal_bridging');
            }
        ]
    ])
}}

{{
    Bootstrap::modal('modal_kedisiplinan', [
        "title" => 'Update Kedisiplinan Pegawai',
        "size" => "modal-md",
        "body" => [
            "content"   => function(){
                return view('table_rekap_absen.modal_kedisiplinan');
            }
        ]
    ])
}}

<script>
      $(document).ready(()=>{
            $("#btn-prestige").click(()=>{
            $("#modal_bridging").modal("show");
           
        })
        $("#btn-disiplin").click(()=>{
            $("#modal_kedisiplinan").modal("show");
           
        })
        $("#filter_bulan").change(()=>{
                tb_table_prestige.draw();
        });
    })
</script>

