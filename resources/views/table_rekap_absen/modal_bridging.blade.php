<?php
use fidpro\builder\Bootstrap;
use fidpro\builder\Widget;
Widget::_init(["select2","datepicker"]);
?>

    <div class="card-body">
       
        <div class="row">
            <div class="col-sm-12">
                {!! 
                    Widget::datepicker("bulan_update",[
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
            <div class="col-sm-12">
            {!!
                Widget::select2("emp_no",[
                    "data" => [
                        "model" => "Employee",
                        "filter" => ["emp_active" => "t"],
                        "column" => ["emp_no","emp_name"]
                    ],                    
                    "extra" => [
                        "required"  => true
                    ]
                ])->render("group","List Pegawai")
            !!}
            </div>           
        </div>    

    </div>
    <div class="card-header text-center">
            <button class="btn btn-info" id="load-data">Tampilkan</button>
        </div>
<script>
 $(document).ready(()=>{
    $("#load-data").click(()=>{
       update_skor();      
        })
    })
    function update_skor(){
        Swal.fire({
            title: 'Ambil Data Absen ?',           
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => { 
            if(result){
                $.ajax({
                     'beforeSend': function() {
                            showLoading();
                        },
                        url: '{{ url("api/prestige/get_rekap_presensi_absen") }}', 
                        type: 'POST', 
                        dataType: 'json', 
                        contentType: 'application/json', 
                        data: JSON.stringify({
                            bulan_update: $("#bulan_update").val(),
                            nip : $("#emp_no").val()
                           
                        }),
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
            }
        });
       

    }
</script>