<?php
use \fidpro\builder\Create;
?>
    {!! Form::open(['route' => 'komponen_jasa.store','id'=>'form_komponen_jasa']) !!}
    <div class="card-body">
        {!! Form::hidden('komponen_id', $komponen_jasa->komponen_id, array('id' => 'komponen_id')) !!}
        {!! Create::input("komponen_kode",[
                    "value"     => $komponen_jasa->komponen_kode,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("komponen_nama",[
                    "value"     => $komponen_jasa->komponen_nama,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("komponen_percentase",[
                    "value"     => $komponen_jasa->komponen_percentase,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("has_detail",[
                    "value"     => $komponen_jasa->has_detail,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("komponen_parent",[
                    "value"     => $komponen_jasa->komponen_parent,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("is_vip",[
                    "value"     => $komponen_jasa->is_vip,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("has_child",[
                    "value"     => $komponen_jasa->has_child,
                    
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
        $('#form_komponen_jasa').parsley().on('field:validated', function() {
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
                        'data': $('#form_komponen_jasa').serialize(),
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