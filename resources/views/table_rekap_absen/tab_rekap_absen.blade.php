@extends('templates.layout')
@section('content')
<?php
use App\Models\Ms_reff;
use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded" id="page_group_refference">
    <div class="card-body">
    <?=
             Bootstrap::tabs([
                "tabs"  => [
                    "Rekap Absensi Pegawai"  => [
                        "href"      => "table_rekap_absen",
                        "content"   => function(){
                             return view("table_rekap_absen.index");
                        }
                    ],
                    "Perizinan/Cuti Pegawai"  => [
                        "href"      => "",
                        "url"       => ""
                    ]
                ]
            ]);
        ?>
    </div>
</div>
<script>
    $(document).ready(()=>{
        $(".nav-link.active").trigger('click');
    })
</script>
@endsection