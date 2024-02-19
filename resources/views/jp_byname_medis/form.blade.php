<?php
use \fidpro\builder\Create;
?>
    {!! Form::open(['route' => 'jp_byname_medis.store','id'=>'form_jp_byname_medis']) !!}
    <div class="card-body">
        {!! Form::hidden('jp_medis_id', $jp_byname_medis->jp_medis_id, array('id' => 'jp_medis_id')) !!}
        {!! Create::input("jaspel_detail_id",[
                    "value"     => $jp_byname_medis->jaspel_detail_id,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("kodepegawai",[
                    "value"     => $jp_byname_medis->kodepegawai,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("nama_pegawai",[
                    "value"     => $jp_byname_medis->nama_pegawai,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("skor",[
                    "value"     => $jp_byname_medis->skor,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("nominal_terima",[
                    "value"     => $jp_byname_medis->nominal_terima,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("jaspel_id",[
                    "value"     => $jp_byname_medis->jaspel_id,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("pencairan_id",[
                    "value"     => $jp_byname_medis->pencairan_id,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("emp_id",[
                    "value"     => $jp_byname_medis->emp_id,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("komponen_id",[
                    "value"     => $jp_byname_medis->komponen_id,
                    
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
        $('#form_jp_byname_medis').parsley().on('field:validated', function() {
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
                        'data': $('#form_jp_byname_medis').serialize(),
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