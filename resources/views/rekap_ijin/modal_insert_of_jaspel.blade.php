<?php
use fidpro\builder\Bootstrap;
use fidpro\builder\Widget;
Widget::_init(["datepicker","daterangepicker"]);
?>
<div class="card-body">
<div class="row align-items-center">
        <div class="col-md-4">
            {!! Widget::daterangePicker("periode")->render("group","Periode Hari") !!}
        </div>
       
        <div class="col-md-4">
        {!! 
                    Widget::datepicker("bulan_potongan_skor",[
                        "format"		=>"yyyy",
                        "viewMode"		=> "years",
                        "minViewMode"	=> "years",
                        "autoclose"		=> true
                    ],[
                        "readonly"      => true,
                        "value"         => date('Y')
                    ])->render("group","Bulan Potongan Skor")
                !!}
        </div>
        <div class="d-flex">
            <button class="btn btn-info" id="btn-submit">Tampilkan</button>
            
        </div>
    </div>

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
</div>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    $(document).ready(()=>{
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    $("#btn-submit").click(()=>{
        get_data_rekap();
        })
    })
    function convertDateFormat(dateString) {
    let dateParts = dateString.split("/"); 
    return `${dateParts[2]}-${dateParts[0]}-${dateParts[1]}`;
    }

    function get_data_rekap(){
        Swal.fire({
            title: 'Tampilkan Data Rekap Izin ?',  
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => { 
            if(result){
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
                            tgl_mulai  : tgl1,
                            tgl_akhir : tgl2
                           
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
        });
    }

    function list_pegawai(data){
        let pegawaiList = $("#pegawaiList");
        pegawaiList.empty();
        if (data.length > 0) {
        // Menambahkan baris ke dalam tabel untuk setiap pegawai
        data.forEach((pegawai, index) => {
            let row = `
                <tr>
                    <td>${index + 1}</td>
                    <td><input type="checkbox" class="pegawaiCheckbox" data-id="${pegawai.emp_id}" /></td>
                    <td>${pegawai.nama_pegawai}</td>
                    <td>${pegawai.alasan_cuti}</td>
                    <td>${pegawai.lama_cuti}</td>
                    <td>${pegawai.tgl_mulai}</td>
                    <td>${pegawai.tgl_selesai}</td>
                    <td>${pegawai.persentase_skor}</td>
                    <td>${pegawai.bulan_potonganSkor}</td>
                </tr>
            `;
            pegawaiList.append(row);
        });
    } else {
        pegawaiList.append("<tr><td colspan='4'>Tidak ada data pegawai ditemukan</td></tr>");
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

    }
</script>