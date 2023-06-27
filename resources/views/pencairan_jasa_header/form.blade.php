<?php
use \fidpro\builder\Create;
?>
    {!! Form::open(['route' => 'pencairan_jasa_header.store','id'=>'form_pencairan_jasa_header']) !!}
    <div class="card-body">
        {!! Form::hidden('id_cair_header', $pencairan_jasa_header->id_cair_header, array('id' => 'id_cair_header')) !!}
        {!! Create::input("no_pencairan",[
                    "value"     => $pencairan_jasa_header->no_pencairan,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("tanggal_cair",[
                    "value"     => $pencairan_jasa_header->tanggal_cair,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("total_nominal",[
                    "value"     => $pencairan_jasa_header->total_nominal,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("user_act",[
                    "value"     => $pencairan_jasa_header->user_act,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("act_at",[
                    "value"     => $pencairan_jasa_header->act_at,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("keterangan",[
                    "value"     => $pencairan_jasa_header->keterangan,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("is_published",[
                    "value"     => $pencairan_jasa_header->is_published,
                    
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
        $('#form_pencairan_jasa_header').parsley().on('field:validated', function() {
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
                        'data': $('#form_pencairan_jasa_header').serialize(),
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