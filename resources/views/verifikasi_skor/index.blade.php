@extends('templates.layoutNoHeader')
@section('content')
<?php
use fidpro\builder\Widget;
Widget::_init(["datepicker"]);
?>
<h4>UNIT KERJA : {{$unit_kerja}}</h4>
<div class="card border-0 shadow rounded" id="page_potongan_statis">
    <div class="card-header">
        <div class="float-right">
            <button class="btn btn-primary"><i class="fas fa-info-circle"></i></button>
        </div>
        <div class="row">
            <div class="col-sm-3">
                {!! 
                    Widget::datepicker("bulan_skor",[
                        "format"		=>"mm-yyyy",
                        "viewMode"		=> "year",
                        "minViewMode"	=> "year",
                        "autoclose"		=> true
                    ],[
                        "readonly"      => true,
                        "value"         => date('m-Y')
                    ])->render()
                !!}
            </div>
            <button class="btn btn-info" onclick="loadTable()">Tampilkan</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive" id="tableSkor">
        </div>
    </div>
</div>

<script>
    $(document).ready(()=>{
        loadTable();
    })

    function konfirmasi_skor(id) {
        Swal.fire({
            title: 'Anda yakin konfirmasi skor bulanan?',
            text: 'Konfirmasi skor tidak dapat dibatalkan. Skor otomatis terkirim untuk perhitungan remunerasi.',
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.value) {
                $.get('{{url("verifikasi_skor/konfirmasi_skor")}}/'+id,function(resp){
                    resp.success(response.message, "Message : ");
                    location.reload();
                },'json');
            }
        })
    }

    function keluhan_skor(id) {
        Swal.mixin({
            input: 'textarea',
            confirmButtonText: 'Kirim',
            showCancelButton: true,
            cancelButtonText: 'Batal',
        }).queue([
            {
                title: 'Form Keluhan Skor Pegawai',
                text: 'Masukkan detail keluhan skor individu pegawai :',
            },
        ]).then((result) => {
            if (result.value) {
                // Proses teks yang dimasukkan oleh pengguna
                const alasan = result.value[0];
                $.ajax({
                    'data': {
                        alasan: alasan,
                        id  : id
                    },
                    headers: {
                        'X-CSRF-TOKEN': '<?=csrf_token()?>'
                    },
                    'dataType': 'json',
                    'url'        : '{{url("verifikasi_skor/save_keluhan")}}',
                    'type'       : 'post',
                    'success': function(data) {
                        if (data.code == 200) {
                            Swal.fire("Sukses!", data.message, "success").then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire("Oopss...!!", data.message, "error");
                        }
                    }
                });
            }
        });
    }

    function loadTable() {
        $.get('{{url("verifikasi_skor/get_data")}}/'+$("#bulan_skor").val(),function(resp){
            if (resp.code == 200) {
                $("#tableSkor").html(resp.content);
            }else{
                Swal.fire("Oopss...!!", resp.message, "error");
            }
        },'json');
    }
</script>
@endsection