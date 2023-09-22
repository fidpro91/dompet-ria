<?php

use \fidpro\builder\Bootstrap;
?>
<div class="table-responsive">
    {{
        Bootstrap::table("table-data",[
            "class" => "table table-hover"
        ],[
            '#','NO','emp_nip','emp_name','unit_name','bulan_jasa_awal','bulan_jasa_akhir','keterangan'
        ])
    }}
</div>
<script>
    $(document).ready(function() {
        $('#table-data').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url('/employee_off/get_dataTable') }}",
            columns: [{
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
                {
                    "data": 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'emp_nip',
                    name: 'emp_nip',
                },
                {
                    data: 'emp_name',
                    name: 'emp_name',
                },
                {
                    data: 'unit_name',
                    name: 'unit_name',
                },
                {
                    data: 'bulan_jasa_awal',
                    name: 'bulan_jasa_awal',
                },
                {
                    data: 'bulan_jasa_akhir',
                    name: 'bulan_jasa_akhir',
                },
                {
                    data: 'keterangan',
                    name: 'keterangan',
                }
            ]
        });
    })
</script>