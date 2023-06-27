<?php

use \fidpro\builder\Create;
?>
{!! Form::open(['route' => 'kategori_potongan.store','id'=>'form_kategori_potongan']) !!}
<div class="card-body">
    {!! Form::hidden('kategori_potongan_id', $kategori_potongan->kategori_potongan_id, array('id' => 'kategori_potongan_id')) !!}
    {!! Create::input("nama_kategori",[
    "value" => $kategori_potongan->nama_kategori,
    "required" => "true"
    ])->render("group");
    !!}
    {!! 
        Create::dropDown("potongan_type",[
            "data" => [
                ["1"     => "Potongan Sistem"],
                ["2"     => "Potongan Individu"]
            ],
            "selected"  => $kategori_potongan->potongan_type
        ])->render("group")
    !!}
    {!! Create::input("deskripsi_potongan",[
    "value" => $kategori_potongan->deskripsi_potongan
    ])->render("group");
    !!}
    {!! 
        Create::dropDown("is_pajak",[
            "data" => [
                ["t"     => "Ya"],
                ["f"     => "Tidak"]
            ],
            "selected"  => $kategori_potongan->is_pajak
        ])->render("group","Potongan Pajak?")
    !!}
    {!! 
        Create::dropDown("potongan_active",[
            "data" => [
                ["t"     => "Aktif"],
                ["f"     => "Non Aktif"]
            ],
            "selected"  => $kategori_potongan->potongan_active
        ])->render("group")
    !!}
</div>
<div class="card-footer text-center">
    {!! Form::submit('Save',['class' => 'btn btn-success']); !!}
    {!! Form::button('Cancel',['class' => 'btn btn-warning btn-refresh']); !!}
</div>
{!!Form::close()!!}

<script>
    $(document).ready(() => {
        $('#form_kategori_potongan').parsley().on('field:validated', function() {
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
                            'data': $('#form_kategori_potongan').serialize(),
                            'dataType': 'json',
                            'success': function(data) {
                                if (data.success) {
                                    Swal.fire("Sukses!", data.message, "success").then(() => {
                                        location.reload();
                                    });
                                } else {
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