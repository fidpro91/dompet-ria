<?php
use \fidpro\builder\Create;
use \fidpro\builder\Widget;

Widget::_init(["select2"]);
?>
    {!! Form::open(['route' => 'jp_byname_medis.store','id'=>'form_jp_byname_medis']) !!}
    <div class="card-body">
        {!! Form::hidden('jp_medis_id', $jp_byname_medis->jp_medis_id, array('id' => 'jp_medis_id')) !!}
        {!! 
            Widget::select2("emp_id",[
                "data" => [
                    "model"     => "Employee",
                    "column"    => ["emp_id","emp_name"]
                ],
                "selected" => $jp_byname_medis->emp_id,
                "extra"    => [
                    "required"  => true
                ]
            ])->render("group","Nama Pegawai");
        !!}
        {!! 
            Create::input("skor",[
                "value"     => $jp_byname_medis->skor,
                "required"  => true
            ])->render("group"); 
        !!}
    </div>
    <div class="card-footer text-center">
        {!! Form::submit('Save',['class' => 'btn btn-success ']); !!}
        {!! Form::button('Cancel',['class' => 'btn btn-warning btn-refresh']); !!}
    </div>
    {!!Form::close()!!}
<script>
    $(document).ready(()=>{
        $('#form_jp_byname_medis').parsley().on('field:validated', function() {
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
                    if($("#jp_medis_id").val() === ''){
                        $.ajaxSetup({
                            'url'    : '{{route("jp_byname_medis.store")}}'
                        });
                    }
                    $.ajax({
                        'data': $('#form_jp_byname_medis').serialize()+"&jaspel_id="+jaspelId+"&komponen_id="+$("#komponen_id").val(),
                        'dataType': 'json',
                        'success': function(data) {
                            if (data.success) {
                                Swal.fire("Sukses!", data.message, "success").then(() => {
                                    $("#page_jp_byname_medis").html(data.redirect);
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