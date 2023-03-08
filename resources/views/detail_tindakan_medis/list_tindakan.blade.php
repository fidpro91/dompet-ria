<?php
use \fidpro\builder\Bootstrap;
?>
<div class="table-responsive">
    {{
        Bootstrap::table("table-data",[
            "class" => "table table-hover"
        ],[
            '#','NO','tindakan_id','jp_medis_id','tanggal_tindakan','nama_tindakan','tarif_tindakan','id_klasifikasi_jasa','klasifikasi_jasa','percentase_jasa','skor_jasa','qty_tindakan','px_norm','px_name','unit_layanan','unit_layanan_id','visit_id','nip','nama_dokter','unit_vip','penjamin_id','nama_penjamin','status_bayar','tanggal_import','billing_id','status_jasa','jasa_tindakan_bulan'
        ])
    }}
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#table-data').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url('/detail_tindakan_medis/get_dataTable') }}",
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
                    data: 'tindakan_id',
                    name: 'tindakan_id',
                },
                {
                    data: 'jp_medis_id',
                    name: 'jp_medis_id',
                },
                {
                    data: 'tanggal_tindakan',
                    name: 'tanggal_tindakan',
                },
                {
                    data: 'nama_tindakan',
                    name: 'nama_tindakan',
                },
                {
                    data: 'tarif_tindakan',
                    name: 'tarif_tindakan',
                },
                {
                    data: 'id_klasifikasi_jasa',
                    name: 'id_klasifikasi_jasa',
                },
                {
                    data: 'klasifikasi_jasa',
                    name: 'klasifikasi_jasa',
                },
                {
                    data: 'percentase_jasa',
                    name: 'percentase_jasa',
                },
                {
                    data: 'skor_jasa',
                    name: 'skor_jasa',
                },
                {
                    data: 'qty_tindakan',
                    name: 'qty_tindakan',
                },
                {
                    data: 'px_norm',
                    name: 'px_norm',
                },
                {
                    data: 'px_name',
                    name: 'px_name',
                },
                {
                    data: 'unit_layanan',
                    name: 'unit_layanan',
                },
                {
                    data: 'unit_layanan_id',
                    name: 'unit_layanan_id',
                },
                {
                    data: 'visit_id',
                    name: 'visit_id',
                },
                {
                    data: 'nip',
                    name: 'nip',
                },
                {
                    data: 'nama_dokter',
                    name: 'nama_dokter',
                },
                {
                    data: 'unit_vip',
                    name: 'unit_vip',
                },
                {
                    data: 'penjamin_id',
                    name: 'penjamin_id',
                },
                {
                    data: 'nama_penjamin',
                    name: 'nama_penjamin',
                },
                {
                    data: 'status_bayar',
                    name: 'status_bayar',
                },
                {
                    data: 'tanggal_import',
                    name: 'tanggal_import',
                },
                {
                    data: 'billing_id',
                    name: 'billing_id',
                },
                {
                    data: 'status_jasa',
                    name: 'status_jasa',
                },
                {
                    data: 'jasa_tindakan_bulan',
                    name: 'jasa_tindakan_bulan',
                }
            ]
        });
    })
</script>