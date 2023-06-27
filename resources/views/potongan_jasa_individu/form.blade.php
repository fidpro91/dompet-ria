<?php

use \fidpro\builder\Create;
use \fidpro\builder\Widget;
Widget::_init(["select2","inputmask"]);
?>
{!! Form::open(['route' => 'potongan_jasa_individu.store','id'=>'form_potongan_jasa_individu']) !!}
<div class="card-body">
    {!! Form::hidden('pot_ind_id', $potongan_jasa_individu->pot_ind_id, array('id' => 'pot_ind_id')) !!}
    {!! 
        Widget::select2("kategori_potongan",[
            "data" => [
                "model"     => "Kategori_potongan",
                "filter"    => ["potongan_active" => "t","potongan_type" => "2"],
                "column"    => ["kategori_potongan_id","nama_kategori"]
            ],
            "selected"  => $potongan_jasa_individu->kategori_potongan,
            "extra"     => [
                "required"  => true
            ]
        ])->render("group");
    !!}
    {!! 
        Widget::select2("emp_id",[
            "data" => [
                "model"     => "Employee",
                "filter"    => ["emp_active" => "t"],
                "column"    => ["emp_id","emp_name"]
            ],
            "selected"  => $potongan_jasa_individu->emp_id,
            "extra"     => [
                "required"  => true
            ]
        ])->render("group","Nama Pegawai");
    !!}
    {!! 
        Create::dropDown("potongan_type",[
            "data" => [
                ["1"     => "NOMINAL"],
                ["2"     => "PERCENT"]
            ],
            "selected"  => $potongan_jasa_individu->potongan_type,
            "extra"     => [
                "required"  => true
            ]
        ])->render("group","Jenis Potongan");
    !!}
    {!! 
        Widget::inputMask("potongan_value",[
            "prop"      => [
                "value"     => $potongan_jasa_individu->potongan_value,
                "required"  => true,
            ],
            "mask"      => [
                "IDR",[
                    "rightAlign"    => false,
                ]
            ]
        ])->render("group");
    !!}
    {!! 
        Create::dropDown("pot_status",[
            "data" => [
                ["t"     => "AKTIF"],
                ["f"     => "NON AKTIF"]
            ],
            "selected"  => $potongan_jasa_individu->pot_status,
            "extra"     => [
                "required"  => true
            ]
        ])->render("group");
    !!}
    {!! Create::input("pot_note",[
    "value" => $potongan_jasa_individu->pot_note
    ])->render("group");
    !!}
    {!! Create::input("last_angsuran",[
    "value" => $potongan_jasa_individu->last_angsuran,
    ])->render("group");
    !!}
    {!! Create::input("max_angsuran",[
    "value" => $potongan_jasa_individu->max_angsuran
    ])->render("group");
    !!}
</div>
<div class="card-footer text-center">
    {!! Form::submit('Save',['class' => 'btn btn-success']); !!}
    {!! Form::button('Cancel',['class' => 'btn btn-warning btn-refresh']); !!}
</div>
{!!Form::close()!!}

<script>
    $(document).ready(() => {
        $('#form_potongan_jasa_individu').parsley().on('field:validated', function() {
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
                            'data': $('#form_potongan_jasa_individu').serialize(),
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