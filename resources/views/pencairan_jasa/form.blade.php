<?php
use \fidpro\builder\Create;
?>
    {!! Form::open(['route' => 'pencairan_jasa.store','id'=>'form_pencairan_jasa']) !!}
    <div class="card-body">
        {!! Form::hidden('id_cair', $pencairan_jasa->id_cair, array('id' => 'id_cair')) !!}
        {!! Create::input("no_pencairan",[
                    "value"     => $pencairan_jasa->no_pencairan,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("tanggal_cair",[
                    "value"     => $pencairan_jasa->tanggal_cair,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("create_by",[
                    "value"     => $pencairan_jasa->create_by,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("create_date",[
                    "value"     => $pencairan_jasa->create_date,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("emp_id",[
                    "value"     => $pencairan_jasa->emp_id,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("total_brutto",[
                    "value"     => $pencairan_jasa->total_brutto,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("total_potongan",[
                    "value"     => $pencairan_jasa->total_potongan,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("total_netto",[
                    "value"     => $pencairan_jasa->total_netto,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("jaspel_id",[
                    "value"     => $pencairan_jasa->jaspel_id,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("id_header",[
                    "value"     => $pencairan_jasa->id_header,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("nomor_rekening",[
                    "value"     => $pencairan_jasa->nomor_rekening,
                    
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
        $('#form_pencairan_jasa').parsley().on('field:validated', function() {
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
                        'data': $('#form_pencairan_jasa').serialize(),
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