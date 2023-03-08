<?php
use \fidpro\builder\Create;
?>
    {!! Form::open(['route' => 'komponen_jasa_sistem.store','id'=>'form_komponen_jasa_sistem']) !!}
    <div class="card-body">
        {!! Form::hidden('id', $komponen_jasa_sistem->id, array('id' => 'id')) !!}
        {!! Create::input("kode_komponen",[
                    "value"     => $komponen_jasa_sistem->kode_komponen,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
        {!! Create::input("nama_komponen",[
            "value"     => $komponen_jasa_sistem->nama_komponen,
            "required"  => "true"
            ])->render("group"); 
        !!}
        {!! Create::input("percentase_jasa",[
            "value"     => $komponen_jasa_sistem->percentase_jasa,
            "required"  => "true"
            ])->render("group"); 
        !!}
        {!! Create::input("deskripsi_komponen",[
            "value"     => $komponen_jasa_sistem->deskripsi_komponen
            ])->render("group"); 
        !!}
        {!! 
            Create::dropDown("type_jasa",[
                "data" => [
                    "model"     => "Ms_reff",
                    "filter"    => ["reffcat_id" => 6],
                    "column"    => ["reff_code","reff_name"]
                ],
                "extra" => [
                    "required" => "true"
                ],
                "selected"  => $komponen_jasa_sistem->type_jasa
            ])->render("group","Teknis Pembagian Jasa")
        !!}
        {!! 
            Create::dropDown("for_medis",[
                "data" => [
                    ["t" => "Ya"],
                    ["f" => "tidak"]
                ],
                "extra" => [
                    "required" => "true"
                ],
                "selected"  => $komponen_jasa_sistem->for_medis
            ])->render("group","Jasa Medis?")
        !!}
        {!! 
            Create::dropDown("komponen_active",[
                "data" => [
                    ["t" => "Aktif"],
                    ["f" => "Non Aktif"]
                ],
                "selected"  => $komponen_jasa_sistem->komponen_active,
                "extra" => [
                    "required" => "true"
                ]
            ])->render("group")
        !!}
    </div>
    <div class="card-footer text-center">
        {!! Form::submit('Save',['class' => 'btn btn-success']); !!}
        {!! Form::button('Cancel',['class' => 'btn btn-warning btn-refresh']); !!}
    </div>
    {!!Form::close()!!}

<script>
    $(document).ready(()=>{
        $('#form_komponen_jasa_sistem').parsley().on('field:validated', function() {
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
                    if($("#id").val() === ''){
                        $.ajaxSetup({
                            'url'    : '{{route("komponen_jasa_sistem.store")}}'
                        });
                    }
                    $.ajax({
                        'data': $('#form_komponen_jasa_sistem').serialize(),
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