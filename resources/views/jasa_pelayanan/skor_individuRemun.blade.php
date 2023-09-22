<?php
use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded" id="page_skor_pegawai">
    <div class="card-body">
        <div class="table-responsive">
            {{
                Bootstrap::DataTable("table-data-skor",[
                    "class" => "table table-hover"
                ],[
                    "url"   => "skor_pegawai/get_dataTable",
                    "filter"    => ["prepare_remun" => "'t'"],
                    "raw"   => [
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
                        'unit_name' => [
                            "data"  => "unit_name",
                            "name"  => "mu.unit_name"
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