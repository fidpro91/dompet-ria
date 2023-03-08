<?php
use \fidpro\builder\Create;
use \fidpro\builder\Widget;
Widget::_init(["select2","daterangepicker"]);
?>
    {!! Form::open(['route' => 'tugas_tambahan.store','id'=>'form_tugas_tambahan']) !!}
    <div class="card-body">
        {!! Form::hidden('id', $tugas_tambahan->id, array('id' => 'id')) !!}
        {!! Create::input("nama_tugas",[
            "value"     => $tugas_tambahan->nama_tugas,
            "required"  => "true"
            ])->render("group"); 
        !!}
        {!! 
            Widget::select2("pemberi_tugas",[
                "data" => [
                    "model"     => "Employee",
                    "filter"    => ["emp_active" => "t"],
                    "column"    => ["emp_id","emp_name"]
                ]
            ])->render("group")
        !!}
        {!! Create::input("nomor_sk",[
            "value"     => $tugas_tambahan->nomor_sk,
            "required"  => "true"
            ])->render("group"); 
        !!}
        {!! 
            Widget::select2("emp_id",[
                "data" => [
                    "model"     => "Employee",
                    "filter"    => ["emp_active" => "t"],
                    "column"    => ["emp_id","emp_name"]
                ]
            ])->render("group","Yang Bertugas")
        !!}
        {!!
            Widget::daterangePicker("tanggal_tugas")->render("group")    
        !!}
        {!! Create::input("deskripsi_tugas",[
            "value"     => $tugas_tambahan->deskripsi_tugas
            ])->render("group"); 
        !!}
        {!! 
            Create::dropDown("jabatan_tugas",[
                "data" => [
                    "model"     => "Detail_indikator",
                    "filter"    => ["indikator_id" => 10],
                    "column"    => ["detail_id","detail_name"]
                ]
            ])->render("group")
        !!}
        {!! Create::upload("file_sk",[
            "value"     => $tugas_tambahan->file_sk
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
        $('#form_tugas_tambahan').parsley().on('field:validated', function() {
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
                    var formData = new FormData($("#form_tugas_tambahan")[0]);
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