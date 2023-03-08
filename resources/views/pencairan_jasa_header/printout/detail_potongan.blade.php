<?php
use fidpro\builder\Widget;
Widget::_init(["inputmask"]);
?>
<div class="table-responsive">
    <table class="table tabel_pajak" style="width: 110% !important;">
        <thead>
            <tr>
                <th>NO</th>
                <th>NIP</th>
                <th>NAMA</th>
                <th>GOLONGAN</th>
                <th>JASA BRUTTO</th>
                <th style="width: 15% !important;">PENGHASILAN WAJIB PAJAK</th>
                <th>PERCENTASE PAJAK</th>
                <th style="width: 20% !important;">PAJAK</th>
                <th>AKUMULASI PENDAPATAN</th>
                <th>#</th>
            </tr>
        </thead>
        <tbody>
            @foreach($potongan as $x => $v)
                <tr>
                    <td>{{$x+1}}</td>
                    <td>{{$v->emp_no}}</td>
                    <td>{{$v->emp_name}}</td>
                    <td>{{$v->golongan}}</td>
                    <td>{{$v->jasa_brutto}}</td>
                    <td>
                        <input type="text" class="form-control input-sm money" name="penghasilan_pajak" value="{{$v->penghasilan_pajak}}">
                    </td>
                    <td>
                        <input type="text" class="form-control input-sm money" name="percentase_pajak" value="{{$v->percentase_pajak}}">
                    </td>
                    <td>
                        <input type="text" class="form-control input-sm money" name="potongan_value" value="{{$v->potongan_value}}">    
                    </td>
                    <td>
                        <input type="text" class="form-control input-sm money" name="akumulasi_penghasilan_pajak" value="{{$v->akumulasi_penghasilan_pajak}}">       
                    </td>
                    <td>
                        <button class="btn btn-xs btn-primary" onclick="update_row(this,'{{$v->potongan_id}}')"><i class="fas fa-check"></i></button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>
    $(document).ready(()=>{
        $(".tabel_pajak").DataTable({
            ordering: false,
            "drawCallback": function( settings ) {
                $('.money').inputmask("IDR");
            },
            "columnDefs" : [
                {
                    'targets': 4,
                    'render' : $.fn.dataTable.render.number( ',', '.', 2)
                }
            ]
        });
    })
</script>