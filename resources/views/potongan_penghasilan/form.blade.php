<?php
use \fidpro\builder\Create;
use \fidpro\builder\Widget;

Widget::_init(["select2"]);
?>
    {!! Form::open(['route' => 'potongan_penghasilan.store','id'=>'form_potongan_penghasilan']) !!}
    <div class="card-body">
        {!! Form::hidden('id', $potongan_penghasilan->id, array('id' => 'id')) !!}
        {!! 
            Widget::select2("id_cair_header",[
                "data" => [
                    "model"     => "Pencairan_jasa_header",
                    "filter"    => ["is_published" => "0"],
                    "column"    => ["id_cair_header","no_pencairan"]
                ],
                "selected"  => $potongan_penghasilan->id_cair_header,
                "extra"     => [
                    "required"  => true
                ]
            ])->render("group","Nomor Pencairan Jasa");
        !!}
        {!!
            Create::dropDown("potongan_method",[
                "data" => [
                    ["1"     => "Sebelum Pajak"],
                    ["2"     => "Setelah Pajak"]
                ],
                "selected"  => $potongan_penghasilan->potongan_method,
                "extra"     => [
                    "required"  => true
                ]
            ])->render("group","Jenis potongan");
        !!}
    </div>
    <div class="card-footer text-center">
        {!! Form::submit('Save',['class' => 'btn btn-success']); !!}
        {!! Form::button('Cancel',['class' => 'btn btn-warning btn-refresh']); !!}
    </div>
    {!!Form::close()!!}

<script>
    $(document).ready(()=>{
        $('#form_potongan_penghasilan').parsley().on('field:validated', function() {
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
                        'data': $('#form_potongan_penghasilan').serialize()+"&kategori_potongan="+$("#kategori_potongan").val(),
                        'dataType': 'json',
                        'beforeSend': function() {
                            showLoading();
                        },
                        'success': function(data) {
                            if (data.success) {
                                Swal.fire({
                                    title: "Sukses!",
                                    text: resp.message,
                                    type: "success",
                                    timer: 1500,  // Waktu dalam milidetik sebelum SweetAlert ditutup otomatis
                                    onClose : () => {
                                        location.reload();
                                    }
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