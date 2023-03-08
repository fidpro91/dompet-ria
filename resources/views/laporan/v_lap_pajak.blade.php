<?php
use fidpro\builder\Bootstrap;
?>
<style>
    .table {
        border-collapse: collapse;
        width: 100%;
        border: 1px solid;
    }
    th {
        border: 1px solid black;
        font-weight: 100pt;
    }
    .table td {
        padding: 5px;
        font-size: 7pt;
        border: 1px solid;
    }
    .body-tabel td {
        border: 1px !important;
        border-color: #fff !important;
    }
    .header th,td {
        padding:0px; margin:0px;
    }
</style>
<h3 style="text-align: center;">LAPORAN PERHITUNGAN PAJAK PENGHASILAN</h3>

{!!
    Bootstrap::tableData($data,["class"=>"table"]);
!!}