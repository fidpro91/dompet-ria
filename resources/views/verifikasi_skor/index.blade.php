@extends('templates.layoutNoHeader')
@section('content')
<?php
use fidpro\builder\Bootstrap;
use fidpro\builder\Widget;
Widget::_init(["datepicker"]);
?>
<style>
    /* Floating chat button styles */
    .chat-button {
        position: fixed;
        bottom: 20px;
        right: 20px; /* Change from left to right */
        z-index: 1050; /* Higher than modal backdrop */
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>
<h4>UNIT KERJA : {{$unit_kerja}}</h4>
<div class="card border-0 shadow rounded" id="page_potongan_statis">
    <div class="card-header">
        <div class="float-right">
            <button class="btn btn-primary" id="btn-info"><i class="fas fa-info-circle"></i></button>
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
                        "value"         => date('m-Y',strtotime('-1 month'))
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
<button type="button" class="btn btn-primary btn-lg chat-button" data-bs-toggle="modal" data-bs-target="#modal_log">
    <i class="mdi mdi-chat-processing"></i>
</button>
{{
    Bootstrap::modal('modal_info',[
        "title"   => 'Informasi Indikator Skor Pegawai',
        "size"    => "modal-lg",
        "body"    => [
            "content"   => function(){
                return view('verifikasi_skor.info_indikator');
            }
        ]
    ])
}}
{{
    Bootstrap::modal('modal_log',[
        "title"   => 'Log Informasi Pengaduan Skor',
        "size"    => "modal-lg"
    ])
}}
<script>
    $(document).ready(()=>{
        loadTable();
        $("#btn-info").click(()=>{
            $("#modal_info").modal("show");
        });
        $(".chat-button").click(()=>{
            let bulan_skor = $("#bulan_skor").val();
            $("#modal_log").find(".modal-body").load(`{{ url('verifikasi_skor/get_keluhan_respon') }}/${bulan_skor}`);
            $("#modal_log").modal("show");
        })
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
                    Swal.fire("Sukses!", resp.message, "success");
                    loadTable();
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