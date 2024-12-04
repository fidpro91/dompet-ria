<?php

use fidpro\builder\Bootstrap;
use fidpro\builder\Widget;

Widget::_init(["datepicker", "daterangepicker","inputmask"]);
?>
<div class="card-body">
    <div class="row align-items-center">
        <div class="col-md-4">
            {!! Widget::daterangePicker("periode")->render("group","Periode Hari") !!}
        </div>
        <div class="col-md-4">
            {!!
                Widget::datepicker("bulan_potongan_skor",[
                    "format" =>"mm-yyyy",
                    "viewMode" => "months",
                    "minViewMode" => "months",
                    "autoclose" => true
            ],[
                "readonly" => true,
                "value" => date('m-Y')
            ])->render("group","Bulan Potongan Skor")
            !!}
        </div>
        <div class="d-flex">
            <button class="btn btn-info" id="btn-submit">Tampilkan</button>
        </div>
        <div class="d-flex">
            <button class="btn btn-danger" id="btn-get">Insert</button>
        </div>
    </div>
    {!! Form::open(['id'=>'form_skor']) !!}
    <table id="pegawaiTable" class="table">
        <thead>
            <tr>
                <th>No</th>
                <th><input type="checkbox" id="selectAll" /></th>
                <th>Nama Pegawai</th>
                <th>Alasan Cuti</th>
                <th>Lama Izin</th>
                <th>Tgl Mulai</th>
                <th>Tgl Akhir</th>
                <th>Persentase Skor</th>
                <th>Bulan Potongan Skor</th>
            </tr>
        </thead>
        <tbody id="pegawaiList">
        </tbody>
    </table>
    {!!Form::close()!!}
</div>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    $(document).ready(() => {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $("#btn-submit").click(() => {
            get_data_rekap();
        })
        $("#btn-get").click(() => {
            insert_to();
        })
    })

    function convertDateFormat(dateString) {
        let dateParts = dateString.split("/");
        return `${dateParts[2]}-${dateParts[0]}-${dateParts[1]}`;
    }

    function get_data_rekap() {
        let periode = $("#periode").val();
        let tanggalArray = periode.split(" - ");
        let tgl1 = convertDateFormat(tanggalArray[0]);
        let tgl2 = convertDateFormat(tanggalArray[1]);
        $.ajax({
            'beforeSend': function() {
                showLoading();
            },
            url: '{{ url("rekap_ijin/potongan_insentif") }}',
            type: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify({
                tgl_mulai: tgl1,
                tgl_akhir: tgl2,
                bulan_skor: $('#bulan_potongan_skor').val()
            }),
            success: function(data) {
                if (data.code == 200) {
                    Swal.fire("Sukses!", data.message, "success").then(() => {
                        list_pegawai(data.data);
                    });
                } else {
                    Swal.fire("Oopss...!!", data.message, "error");
                }
            },
            error: function(xhr, status, error) {
                Swal.fire("Error!", "Terjadi kesalahan saat memproses permintaan.", "error");
            }
        });

    }
    function list_pegawai(data) {
        let pegawaiList = $("#pegawaiList");
        pegawaiList.empty();
        if (data.length > 0) {
            // Menambahkan baris ke dalam tabel untuk setiap pegawai
            data.forEach((pegawai, index) => {
                let row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td><input type="checkbox" name="pegawai_skor[${index}][id]" class="pegawaiCheckbox" data-id="${pegawai.id}" value="${pegawai.id}"/></td>
                            <td>${pegawai.nama_pegawai}</td>
                            <td>${pegawai.alasan_cuti}</td>
                            <td>${pegawai.lama_cuti}</td>
                            <td>${pegawai.tgl_mulai}</td>
                            <td>${pegawai.tgl_selesai}</td>
                            <td>${pegawai.persentase_skor}</td>
                            <td>
                                <input type="text" name="pegawai_skor[${index}][bulan_skor]" class="form-control input-sm bulan_skor" value="${pegawai.bulan_potonganSkor}" />
                            </td>
                        </tr>
                    `;
                pegawaiList.append(row);
            });
            $(".bulan_skor").inputmask("99-9999");
        } else {
            pegawaiList.append("<tr class='text-center'><td colspan='10'>Tidak ada data pegawai ditemukan</td></tr>");
        }


    }
    $('#selectAll').on('click', function() {
        let isChecked = $(this).prop('checked');
        $('.pegawaiCheckbox').prop('checked', isChecked);
    });
    $('.pegawaiCheckbox').on('change', function() {
        let isChecked = $(this).prop('checked');
        if (!isChecked) {
            $('#selectAll').prop('checked', false);
        }
    });

    function insert_to() {
        let selectedIds = [];
        $('.pegawaiCheckbox:checked').each(function() {
            selectedIds.push($(this).data('id'));
        });
        Swal.fire({
            title: 'Insert ke Pegawai of Jaspel ?',
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result) {
                if (selectedIds.length > 0) {
                    $.ajax({
                        'beforeSend': function() {
                            showLoading();
                        },
                        url: '{{ url("rekap_ijin/add_potongan_skor") }}',
                        type: 'POST',
                        dataType: 'json',
                        data: $('#form_skor').serialize(),
                        success: function(data) {
                            if (data.code == 200) {
                                Swal.fire("Sukses!", data.message, "success").then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire("Oopss...!!", data.message, "error");
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire("Error!", "Terjadi kesalahan saat memproses permintaan.", "error");
                        }
                    });

                } else {
                    Swal.fire("Error!", "Pilih pegawai terlebih dahulu", "error");
                }

            }
        });


    }
</script>