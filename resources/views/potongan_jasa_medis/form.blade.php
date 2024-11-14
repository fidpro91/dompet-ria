<?php
use \fidpro\builder\Create;
use \fidpro\builder\Widget;

Widget::_init(["select2"]);
?>
    {!! Form::open(['route' => 'potongan_jasa_medis.store','id'=>'form_potongan_jasa_medis']) !!}
    <div class="card-body">
        {!! Form::hidden('potongan_id', $potongan_jasa_medis->potongan_id, array('id' => 'potongan_id')) !!}
        {!! 
            Widget::select2("pencairan_id",[
                "data" => $pegawai,
                "extra"     => [
                    "required"  => true
                ]
            ])->render("group","Nama Pegawai");
        !!}
        {!! 
            Create::dropDown("jenis_potongan",[
                "data" => [
                    "model"     => "Ms_reff",
                    "filter"    => [
                        "reffcat_id"    => 9,
                        "reff_active"   => 't',
                    ],
                    "column"    => ["reff_id","reff_name"]
                ],
                "extra"     => [
                    "required"  => true
                ]
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
        $('#form_potongan_jasa_medis').parsley().on('field:validated', function() {
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
                        'data': $('#form_potongan_jasa_medis').serialize()+"&kategori_potongan="+$("#kategori_potongan").val(),
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