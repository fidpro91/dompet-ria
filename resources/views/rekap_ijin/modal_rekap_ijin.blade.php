<?php
use fidpro\builder\Bootstrap;
use fidpro\builder\Widget;
Widget::_init(["select2","datepicker"]);
?>

    <div class="card-body">
       
        <div class="row">
            <div class="col-sm-12">
                {!! 
                    Widget::datepicker("tahun",[
                        "format"		=>"yyyy",
                        "viewMode"		=> "years",
                        "minViewMode"	=> "years",
                        "autoclose"		=> true
                    ],[
                        "readonly"      => true,
                        "value"         => date('Y')
                    ])->render()
                !!}
            </div>    
            <div class="col-sm-12">
            {!!
                Widget::select2("pegawai",[
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
            <button class="btn btn-info" id="btn-tampil">Tampilkan</button>
        </div>
<script>
 $(document).ready(()=>{
    $("#btn-tampil").click(()=>{
        get_rekap_ijin();
        })
    })
   
    function get_rekap_ijin(){       
        Swal.fire({
            title: 'Ambil Data Rekap Izin ?',  
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
                        url: '{{ url("api/prestige/rekap_ijin") }}', 
                        type: 'POST', 
                        dataType: 'json', 
                        contentType: 'application/json', 
                        data: JSON.stringify({
                            tahun_update: $("#tahun").val(),
                            nip : $("#pegawai").val()
                           
                        }),
                        success: function(data) {
                            if (data.code == 200) {
                                Swal.fire("Sukses!", data.message, "success").then(() => {
                                    $("#modal_rekapIjin").modal("hide");
                                    tb_table_rekap_izin.draw();
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