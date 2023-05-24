<?php
use \fidpro\builder\Create;
?>
    {!! Form::open(['route' => 'repository_download.store','id'=>'form_repository_download']) !!}
    <div class="card-body">
        {!! Form::hidden('id', $repository_download->id, array('id' => 'id')) !!}
        {!! Create::input("download_date",[
                    "value"     => $repository_download->download_date,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("bulan_jasa",[
                    "value"     => $repository_download->bulan_jasa,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("bulan_pelayanan",[
                    "value"     => $repository_download->bulan_pelayanan,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("periode_awal",[
                    "value"     => $repository_download->periode_awal,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("periode_akhir",[
                    "value"     => $repository_download->periode_akhir,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("group_penjamin",[
                    "value"     => $repository_download->group_penjamin,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("jenis_pembayaran",[
                    "value"     => $repository_download->jenis_pembayaran,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("download_by",[
                    "value"     => $repository_download->download_by,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("created_at",[
                    "value"     => $repository_download->created_at,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("updated_at",[
                    "value"     => $repository_download->updated_at,
                    
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
        $('#form_repository_download').parsley().on('field:validated', function() {
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
                        'data': $('#form_repository_download').serialize(),
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