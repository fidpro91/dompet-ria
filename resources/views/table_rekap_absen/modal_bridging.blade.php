<?php
use fidpro\builder\Bootstrap;
use fidpro\builder\Widget;
Widget::_init(["datepicker"]);
?>

    <div class="card-body">
       
        <div class="row">
            <div class="col-sm-3">
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
            <button class="btn btn-info" id="load-data">Tampilkan</button>
        </div>
    </div>
<script>
 $(document).ready(()=>{
    $("#load-data").click(()=>{
        update_skor();
        })
    })
    function update_skor(){
        $.ajax({
                    'data': {
                        bulan_update: $("#bulan_update").val();
                    },                   
                    'dataType': 'json',
                    'url'        : '{{url("api/prestige/get_rekap_presensi_absen")}}',
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
</script>