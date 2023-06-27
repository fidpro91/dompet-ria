<?php
use \fidpro\builder\Create;
?>
    {!! Form::open(['route' => 'klasifikasi_pajak_penghasilan.store','id'=>'form_klasifikasi_pajak_penghasilan']) !!}
    <div class="card-body">
        {!! Form::hidden('range_id', $klasifikasi_pajak_penghasilan->range_id, array('id' => 'range_id')) !!}
        {!! Create::input("nama_range",[
                    "value"     => $klasifikasi_pajak_penghasilan->nama_range,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("batas_bawah",[
                    "value"     => $klasifikasi_pajak_penghasilan->batas_bawah,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("batas_atas",[
                    "value"     => $klasifikasi_pajak_penghasilan->batas_atas,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("percentase_pajak",[
                    "value"     => $klasifikasi_pajak_penghasilan->percentase_pajak,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("keterangan",[
                    "value"     => $klasifikasi_pajak_penghasilan->keterangan,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("range_status",[
                    "value"     => $klasifikasi_pajak_penghasilan->range_status,
                    
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
        $('#form_klasifikasi_pajak_penghasilan').parsley().on('field:validated', function() {
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
                        'data': $('#form_klasifikasi_pajak_penghasilan').serialize(),
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