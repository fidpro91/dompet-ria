<?php
use \fidpro\builder\Create;
?>
    {!! Form::open(['route' => 'proporsi_jasa_individu.store','id'=>'form_proporsi_jasa_individu']) !!}
    <div class="card-body">
        {!! Form::hidden('proporsi_id', $proporsi_jasa_individu->proporsi_id, array('id' => 'proporsi_id')) !!}
        {!! Create::input("employee_id",[
                    "value"     => $proporsi_jasa_individu->employee_id,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("komponen_id",[
                    "value"     => $proporsi_jasa_individu->komponen_id,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("jasa_bulan",[
                    "value"     => $proporsi_jasa_individu->jasa_bulan,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("is_used",[
                    "value"     => $proporsi_jasa_individu->is_used,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("id_jaspel",[
                    "value"     => $proporsi_jasa_individu->id_jaspel,
                    
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
        $('#form_proporsi_jasa_individu').parsley().on('field:validated', function() {
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
                        'data': $('#form_proporsi_jasa_individu').serialize(),
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