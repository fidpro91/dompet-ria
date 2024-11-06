<?php
use \fidpro\builder\Create;
?>
    {!! Form::open(['route' => 'rekap_ijin.store','id'=>'form_rekap_ijin']) !!}
    <div class="card-body">
        {!! Form::hidden('id', $rekap_ijin->id, array('id' => 'id')) !!}
        {!! Create::input("nip",[
                    "value"     => $rekap_ijin->nip,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("nama_pegawai",[
                    "value"     => $rekap_ijin->nama_pegawai,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("jenis_ijin",[
                    "value"     => $rekap_ijin->jenis_ijin,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("tipe_ijin",[
                    "value"     => $rekap_ijin->tipe_ijin,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("tgl_mulai",[
                    "value"     => $rekap_ijin->tgl_mulai,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("tgl_selesai",[
                    "value"     => $rekap_ijin->tgl_selesai,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("lama_ijin",[
                    "value"     => $rekap_ijin->lama_ijin,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("keterangan",[
                    "value"     => $rekap_ijin->keterangan,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("created_at",[
                    "value"     => $rekap_ijin->created_at,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("updated_at",[
                    "value"     => $rekap_ijin->updated_at,
                    
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
        $('#form_rekap_ijin').parsley().on('field:validated', function() {
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
                        'data': $('#form_rekap_ijin').serialize(),
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