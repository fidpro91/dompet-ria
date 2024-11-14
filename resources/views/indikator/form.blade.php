<?php
use \fidpro\builder\Create;
?>
{!! Form::open(['route' => 'indikator.store','id'=>'form_indikator']) !!}
<div class="card-body">
    {!! Form::hidden('id', $indikator->id, array('id' => 'id')) !!}
    {!! Create::input("kode_indikator",[
    "value" => $indikator->kode_indikator,
    "required" => "true"
    ])->render("group");
    !!}
    {!! Create::input("indikator",[
    "value" => $indikator->indikator,
    "required" => "true"
    ])->render("group");
    !!}
    {!! Create::input("deskripsi",[
    "value" => $indikator->deskripsi
    ])->render("group");
    !!}
    {!! Create::input("bobot",[
    "value"     => $indikator->bobot,
    "required"  => "true"
    ])->render("group");
    !!}
    {!! 
        Create::dropDown("group_index",[
            "data" => [
                "model"     => "Ms_reff",
                "filter"    => ["reffcat_id" => 3],
                "column"    => ["reff_id","reff_name"]
            ],
            "selected"  => $indikator->group_index
        ])->render("group")
    !!}
    {!! 
        Create::dropDown("status_indikator",[
        "data" => [
            ["t" => "Aktif"],
            ["f" => "Non Aktif"]
        ],
        "selected"  => $indikator->status
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
        $('#form_indikator').parsley().on('field:validated', function() {
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
                            'data': $('#form_indikator').serialize(),
                            'dataType': 'json',
                            'beforeSend': function() {
                                showLoading();
                            },
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