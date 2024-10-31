<?php
use \fidpro\builder\Create;
?>
    {!! Form::open(['route' => 'table_rekap_absen.store','id'=>'form_table_rekap_absen']) !!}
    <div class="card-body">
        {!! Form::hidden('id', $table_rekap_absen->id, array('id' => 'id')) !!}
        {!! Create::input("nip",[
                    "value"     => $table_rekap_absen->nip,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("bulan_update",[
                    "value"     => $table_rekap_absen->bulan_update,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("tahun_update",[
                    "value"     => $table_rekap_absen->tahun_update,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("persentase_kehadiran",[
                    "value"     => $table_rekap_absen->persentase_kehadiran,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("keterangan",[
                    "value"     => $table_rekap_absen->keterangan,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("created_at",[
                    "value"     => $table_rekap_absen->created_at,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("updated_at",[
                    "value"     => $table_rekap_absen->updated_at,
                    
                    ])->render("group"); 
                !!}
    </div>
    <div class="card-footer text-center">
        {!! Form::submit('Save',['class' => 'btn btn-success']); !!}
        {!! Form::button('Cancel',['class' => 'btn btn-warning btn-refresh']); !!}
    </div>
    {!!Form::close()!!}

<script>
    $(document).ready(()=>{
        $('#form_table_rekap_absen').parsley().on('field:validated', function() {
            var ok = $('.parsley-error').length === 0;
            $('.bs-callout-info').toggleClass('hidden', !ok);
            $('.bs-callout-warning').toggleClass('hidden', ok);
        })
        .on('form:submit', function() {
            Swal.fire({
                title: 'Simpan Data?',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        'data': $('#form_table_rekap_absen').serialize(),
                        'dataType': 'json',
                        'success': function(data) {
                            if (data.success) {
                                Swal.fire("Sukses!", data.message, "success").then(() => {
                                    location.reload();
                                });
                            }else{
                                Swal.fire("Oopss...!!", data.message, "error");
                            }
                        }
                    });
                }
            })
            return false;
        });
    })
</script>