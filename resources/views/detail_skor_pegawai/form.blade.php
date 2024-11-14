<?php
use \fidpro\builder\Create;
?>
    {!! Form::open(['route' => 'detail_skor_pegawai.store','id'=>'form_detail_skor_pegawai']) !!}
    <div class="card-body">
        {!! Form::hidden('det_skor_id', $detail_skor_pegawai->det_skor_id, array('id' => 'det_skor_id')) !!}
        {!! Create::input("skor_id",[
                    "value"     => $detail_skor_pegawai->skor_id,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("emp_id",[
                    "value"     => $detail_skor_pegawai->emp_id,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("kode_skor",[
                    "value"     => $detail_skor_pegawai->kode_skor,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("detail_skor",[
                    "value"     => $detail_skor_pegawai->detail_skor,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("skor",[
                    "value"     => $detail_skor_pegawai->skor,
                    
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
        $('#form_detail_skor_pegawai').parsley().on('field:validated', function() {
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
                        'data': $('#form_detail_skor_pegawai').serialize(),
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