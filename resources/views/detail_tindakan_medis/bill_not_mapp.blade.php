@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded" id="page_detail_tindakan_medis">
    <div class="card-body">
        <table class="table table-hover" id="table_bill">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>BILL CODE</th>
                    <th>BILL NAME</th>
                    <th>BILL IN UNIT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $x=>$v)
                    <tr>
                        <td>{{($x+1)}}</td>
                        <td>{{($v->bill_code)}}</td>
                        <td>{{($v->bill_name)}}</td>
                        <td>{{($v->unit_name)}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
    $(document).ready(()=>{
        $("#table_bill").DataTable({
            dom: 'Bfrtip',
            buttons: [
                  {
                  "extend": 'pdf',
                  "text": '<i class="fa fa-file-pdf-o" style="color: green;"></i> PDF',
                  "titleAttr": 'PDF',                               
                  "orientation" : 'landscape',
                  "pageSize" : 'LEGAL',
                  "download": 'open'
                },'csv',
                {
                  "extend": 'print',
                  "text": '<i class="fa fa-print" style="color: green;"></i> CETAK',
                  "titleAttr": 'Print',                                
                  "action": newexportaction
                }
            ],
            paging: false,
            ordering: false,
            info: false,
        })
    })
</script>
@endsection