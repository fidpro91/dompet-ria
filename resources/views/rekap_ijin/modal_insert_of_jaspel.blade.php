<?php
use fidpro\builder\Bootstrap;
use fidpro\builder\Widget;
Widget::_init(["datepicker","daterangepicker"]);
?>
<div class="card-body">
<div class="row align-items-center">
        <div class="col-md-6 text-center">
            {!! Widget::daterangePicker("periode")->render("group","Periode Hari") !!}
        </div>
        <div class="d-flex align-items-center">
            <button class="btn btn-info" id="btn-submit">Tampilkan</button>
        </div>
    </div>

    <table id="pegawaiTable" class="table">
    <thead>
        <tr>
            <th><input type="checkbox" id="selectAll" /></th> 
            <th>Nama Pegawai</th>
            <th>Lama Ijin</th>     
            <th>Keterangan</th>      
        </tr>
    </thead>
    <tbody id="pegawaiList">
       
    </tbody>
</table>
</div>
<script>
    $(document).ready(()=>{
    $("#btn-submit").click(()=>{
        get_data_rekap();
        })
    })
    function convertDateFormat(dateString) {
    let dateParts = dateString.split("/"); 
    return `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`;
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
                let tglAwal = convertDateFormat(tanggalArray[0]); 
                let tglAkhir = convertDateFormat(tanggalArray[1]);
              
                $.ajax({
                     'beforeSend': function() {
                            showLoading();
                        },
                        url: '{{ url("rekap_ijin/tampilkan_data_rekap") }}'+'/'+tglAwal+'/'+tglAkhir, 
                        type: 'GET', 
                        dataType: 'json', 
                        contentType: 'application/json',                        
                        success: function(data) {
                            list_pegawai(data);
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
                    <td><input type="checkbox" class="pegawaiCheckbox" data-id="${pegawai.id}" /></td>
                    <td>${pegawai.nama_pegawai}</td>
                    <td>${pegawai.lama_ijin}</td>
                    <td>${pegawai.keterangan}</td>
                </tr>
            `;
            pegawaiList.append(row);
        });
    } else {
        pegawaiList.append("<tr><td colspan='4'>Tidak ada data pegawai ditemukan</td></tr>");
    }

    }
</script>