@extends('templates.layout')
@section('content')
<?php

use App\Libraries\Servant;
use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded">
    <div class="card-header">
        {!!
            Form::button("Final Pencairan Jasa",[
                "class" => "btn btn-purple btn-final"
            ])
        !!}
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <td>TANGGAL PENCAIRAN : </td>
                    <td><?= date('d-m-Y', strtotime($data['header']->tanggal_cair)) ?></td>
                </tr>
                <tr>
                    <td>NOMOR : </td>
                    <td><?= $data['header']->no_pencairan ?></td>
                </tr>
                <tr>
                    <td>TOTAL JASA PELAYANAN :</td>
                    <td><?= convert_currency2($data['header']->total_nominal) ?></td>
                </tr>
            </thead>
        </table>
    </div>
    <div class="card-body">
        <?=
            Bootstrap::tabs([
                "id"        => "tab_pajak",
                "tabs"  => function() use($data){
                    $tabs = [];
                    $potongan = array_filter($data['potongan'], function ($var){
                        return ($var['is_pajak'] == 't');
                    });
                    foreach ($potongan as $key => $value) {
                        $tabs[$value['nama_kategori']] = [
                            "href"      => "link_".$value['kategori_potongan_id'],
                            "url"       => "pencairan_jasa_header/detail/1/".$value['kategori_potongan_id']."/".$data['header']->id_cair_header
                        ];
                    }
                    return $tabs;
                }
            ]);
        ?>
    </div>
    <div class="card-body">
        <?=
            Bootstrap::tabs([
                "id"    => "tab_potongan",
                "tabs"  => function() use($data){
                    $tabs = [];
                    $potongan = array_filter($data['potongan'], function ($var){
                        return ($var['is_pajak'] == 'f');
                    });
                    foreach ($potongan as $key => $value) {
                        $tabs[$value['nama_kategori']] = [
                            "href"      => "po_".$value['kategori_potongan_id'],
                            "url"       => "pencairan_jasa_header/detail/2/".$value['kategori_potongan_id']."/".$data['header']->id_cair_header
                        ];
                    }
                    return $tabs;
                }
            ]);
        ?>
    </div>
</div>
<script>
    $(document).ready(()=>{
        $(".nav-link.active").trigger('click');

        $(".btn-final").click(()=>{
            Swal.fire({
                title: 'Final pencairan jasa?',
                text : 'Hasil perhitungan jasa akan dishare ke pengguna aplikasi. Final pencairan tidak dapat dibatalkan.',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        'dataType': 'json',
                        'type'  : 'get',
                        'beforeSend': function() {
                            showLoading();
                        },
                        'url'   : '<?=url("pencairan_jasa_header/final_pencairan/".$data['header']->id_cair_header."")?>',
                        'success': function(data) {
                            if (data.success) {
                                Swal.fire("Sukses!", data.message, "success").then(() => {
                                    // location.reload();
                                    location.href = "{{url('pencairan_jasa_header')}}";
                                });
                            }else{
                                Swal.fire("Oopss...!!", data.message, "error");
                            }
                        }
                    });
                }
            })
        })
    })

    function hitung_pajak(row) {
        var pendapatan = $(row).closest('tr').find('.penghasilan_pajak').val();
        var pajak = $(row).closest('tr').find('.percentase_pajak').val();
        var hitungPajak = pajak/100*pendapatan;
        $(row).closest('tr').find('.potongan_value').val(hitungPajak);
    }
    
    function update_row(row,id) {
        var data = $(row).closest('tr').find('input').serialize()+"&potongan_id="+id;
        $.ajax({
            'data': data,
            'dataType': 'json',
            'url'  : '{{url("pencairan_jasa_header/update_potongan")}}',
            'type' : 'get',
            'success': function(data) {
                if (data.success) {
                    toastr.success(data.message, "Message : ");
                }else{
                    toastr.error(data.message, "Message : ");
                }
            }
        });
    }
</script>
@endsection