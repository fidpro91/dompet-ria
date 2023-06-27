@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Bootstrap;
use fidpro\builder\Widget;
Widget::_init(["select2"]);
?>
{!!Form::open(["url" => "detail_tindakan_medis/get_data_simrs","id"=>"form_download"])!!}
<div class="card border-0 shadow rounded" id="page_detail_tindakan_medis">
    <div class="card-body">
        <table class="table table-hover" id="table_bill">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>BILL CODE</th>
                    <th>BILL NAME</th>
                    <th>BILL IN UNIT</th>
                    <th width="20%">KLASIFIKASI</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($data))
                    @foreach($data as $x=>$v)
                        <tr>
                            <td>{{($x+1)}}</td>
                            <td>{{($v->bill_code)}}</td>
                            <td>{{($v->bill_name)}}</td>
                            <td>{{($v->unit_name)}}</td>
                            <td>
                                {!! 
                                    Form::hidden('tindakan_id[]', $v->bill_id) 
                                !!}
                                {!! 
                                    Widget::select2("klasifikasi_id_$x",[
                                        "data" => [
                                            "model"     => "Klasifikasi_jasa",
                                            "column"    => ["id_klasifikasi_jasa","klasifikasi_jasa"]
                                        ],
                                        "extra" => [
                                            "onchange"  => "set_mapping(this,$v->bill_id)"
                                        ]
                                    ])->render()
                                !!}
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
{!!Form::close()!!}
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

    function set_mapping(row,id) {
        $.get("{{url('detail_tindakan_medis/set_mapping_bill')}}/"+id+"/"+$(row).val(),function(resp){
            if (resp.metadata.code == "200") {
                toastr.success(resp.metadata.message, "Message : ");
            }else{
                toastr.error(resp.metadata.message, "Message : ");
            }
        },"json");
    }
</script>
@endsection