<?php
use \fidpro\builder\Create;
?>
    {!! Form::open(['route' => 'klasifikasi_jasa.store','id'=>'form_klasifikasi_jasa']) !!}
    <div class="card-body">
        {!! Form::hidden('id_klasifikasi_jasa', $klasifikasi_jasa->id_klasifikasi_jasa, array('id' => 'id_klasifikasi_jasa')) !!}
        {!! Create::input("klasifikasi_jasa",[
                    "value"     => $klasifikasi_jasa->klasifikasi_jasa,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("percentase_eksekutif",[
                    "value"     => $klasifikasi_jasa->percentase_eksekutif,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("percentase_non_eksekutif",[
                    "value"     => $klasifikasi_jasa->percentase_non_eksekutif,
                    
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
        $('#form_klasifikasi_jasa').parsley().on('field:validated', function() {
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
                        'data': $('#form_klasifikasi_jasa').serialize(),
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