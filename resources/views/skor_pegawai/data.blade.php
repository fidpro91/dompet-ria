<?php
use \fidpro\builder\Bootstrap;
use \fidpro\builder\Widget;
Widget::_init(["datepicker"]);
?>
<div class="card border-0 shadow rounded" id="page_skor_pegawai">
    <div class="card-header">
        <div class="row">
            <div class="col-md-9">
                {!!
                    Form::button("Tambah",[
                        "class" => "btn btn-primary add-form",
                        "data-target" => "page_skor_pegawai",
                        "data-url" => route("skor_pegawai.create")
                    ])
                !!}
            </div>
            <div class="col-md-3">
                {!! 
                    Widget::datepicker("bulan_filter",
                    [
                        "format"		=>"mm-yyyy",
                        "viewMode"		=> "year",
                        "minViewMode"	=> "year",
                        "autoclose"		=> true,
                        "orientation"   => "bottom" 
                    ],[
                        "value" => date("m-Y")
                    ])->render()
                !!}
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table_skor",[
                    "class" => "table table-hover"
                ],[
                    "url"       => "skor_pegawai/get_dataTable",
                    "filter"    => ["bulan_update" => "$('#bulan_filter').val()"],
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
                        'emp_no'    => [
                            "data"  => "emp_no",
                            "name"  => "e.emp_no"
                        ],
                        'emp_name'  => [
                            "data"  => "emp_name",
                            "name"  => "e.emp_name"
                        ],
                        'basic_index' => [
                            "data"  => "basic_index",
                            "name"  => "sp.basic_index"
                        ],
                        'capacity_index' => [
                            "data"  => "capacity_index",
                            "name"  => "sp.capacity_index"
                        ],
                        'emergency_index' => [
                            "data"  => "emergency_index",
                            "name"  => "sp.emergency_index"
                        ],
                        'unit_risk_index' => [
                            "data"  => "unit_risk_index",
                            "name"  => "sp.unit_risk_index"
                        ],
                        'position_index' => [
                            "data"  => "position_index",
                            "name"  => "sp.position_index"
                        ],
                        'competency_index' => [
                            "data"  => "competency_index",
                            "name"  => "sp.competency_index"
                        ],
                        'total_skor' => [
                            "data"  => "total_skor",
                            "name"  => "sp.total_skor"
                        ]
                    ]
                ])
            }}
        </div>
    </div>
</div>
<script>
    $(document).ready(()=>{
        $("#bulan_filter").change(()=>{
            tb_table_skor.draw();
        });
    })
</script>