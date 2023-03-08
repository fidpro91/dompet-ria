<?php
use \fidpro\builder\Create;
use \fidpro\builder\Widget;
Widget::_init(["select2","daterangepicker"]);
?>
{!! Form::open(['route' => 'diklat.store','id'=>'form_diklat','enctype'=>'multipart/form-data']) !!}
<div class="card-body">
    {!! Form::hidden('id', $diklat->id, array('id' => 'id')) !!}
    {!!
        Widget::daterangePicker("tanggal_pelatihan")->render("group")    
    !!}
    {!! Create::input("judul_pelatihan",[
    "value" => $diklat->judul_pelatihan,
    "required" => "true"
    ])->render("group");
    !!}
    {!! Create::input("penyelenggara",[
    "value" => $diklat->penyelenggara,
    "required" => "true"
    ])->render("group");
    !!}
    {!! 
        Create::dropDown("indikator_skor",[
            "data" => [
                "model"     => "Detail_indikator",
                "filter"    => ["indikator_id" => 4],
                "column"    => ["detail_id","detail_name"]
            ]
        ])->render("group")
    !!}
    {!! 
        Widget::select2("peserta_id",[
            "data" => [
                "model"     => "Employee",
                "filter"    => ["emp_active" => "t"],
                "column"    => ["emp_id","emp_name"]
            ]
        ])->render("group","Nama Peserta")
    !!}
    {!! 
        Create::input("lokasi_pelatihan",[
            "value" => $diklat->lokasi_pelatihan
        ])->render("group");
    !!}
    {!! 
        Create::upload("sertifikat_file",[
            "value" => $diklat->sertifikat_file
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
        $('#form_diklat').parsley().on('field:validated', function() {
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
                        var formData = new FormData($("#form_diklat")[0]);
                        $.ajax({
                            'data': formData,
                            headers: {
                                'X-CSRF-TOKEN': '<?=csrf_token()?>'
                            },
                            'dataType': 'json',
                            'processData': false,
                            'contentType': false,
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