<?php
use \fidpro\builder\Create;
?>
    {!! Form::open(['route' => 'jasa_pelayanan.store','id'=>'form_jasa_pelayanan']) !!}
    <div class="card-body">
        {!! Form::hidden('jaspel_id', $jasa_pelayanan->jaspel_id, array('id' => 'jaspel_id')) !!}
        {!! Create::input("tanggal_jaspel",[
                    "value"     => $jasa_pelayanan->tanggal_jaspel,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("periode_jaspel",[
                    "value"     => $jasa_pelayanan->periode_jaspel,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("jaspel_bulan",[
                    "value"     => $jasa_pelayanan->jaspel_bulan,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("jaspel_tahun",[
                    "value"     => $jasa_pelayanan->jaspel_tahun,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("kodejaminan",[
                    "value"     => $jasa_pelayanan->kodejaminan,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("namajaminan",[
                    "value"     => $jasa_pelayanan->namajaminan,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("nominal_pendapatan",[
                    "value"     => $jasa_pelayanan->nominal_pendapatan,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("percentase_jaspel",[
                    "value"     => $jasa_pelayanan->percentase_jaspel,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("nominal_jaspel",[
                    "value"     => $jasa_pelayanan->nominal_jaspel,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("created_by",[
                    "value"     => $jasa_pelayanan->created_by,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("created_at",[
                    "value"     => $jasa_pelayanan->created_at,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("status",[
                    "value"     => $jasa_pelayanan->status,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("keterangan",[
                    "value"     => $jasa_pelayanan->keterangan,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("id_cair",[
                    "value"     => $jasa_pelayanan->id_cair,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("no_jasa",[
                    "value"     => $jasa_pelayanan->no_jasa,
                    
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
        $('#form_jasa_pelayanan').parsley().on('field:validated', function() {
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
                        'data': $('#form_jasa_pelayanan').serialize(),
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