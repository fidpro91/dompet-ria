<?php
use \fidpro\builder\Create;
?>
    {!! Form::open(['route' => 'komplain_skor.store','id'=>'form_komplain_skor']) !!}
    <div class="card-body">
        {!! Form::hidden('id_komplain', $komplain_skor->id_komplain, array('id' => 'id_komplain')) !!}
        {!! Create::input("tanggal",[
                    "value"     => $komplain_skor->tanggal,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("id_skor",[
                    "value"     => $komplain_skor->id_skor,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("employee_id",[
                    "value"     => $komplain_skor->employee_id,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("isi_komplain",[
                    "value"     => $komplain_skor->isi_komplain,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("tanggapan_komplain",[
                    "value"     => $komplain_skor->tanggapan_komplain,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("status_komplain",[
                    "value"     => $komplain_skor->status_komplain,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("user_komplain",[
                    "value"     => $komplain_skor->user_komplain,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("user_approve",[
                    "value"     => $komplain_skor->user_approve,
                    
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
        $('#form_komplain_skor').parsley().on('field:validated', function() {
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
                        'data': $('#form_komplain_skor').serialize(),
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