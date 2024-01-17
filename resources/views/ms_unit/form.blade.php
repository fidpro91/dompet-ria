<?php

use \fidpro\builder\Create;
use \fidpro\builder\Widget;

Widget::_init(["select2"]);
?>
{!! Form::open(['route' => 'ms_unit.store','id'=>'form_ms_unit']) !!}
<div class="card-body">
    {!! Form::hidden('unit_id', $ms_unit->unit_id, array('id' => 'unit_id')) !!}
    {!! 
        Create::input("unit_name",[
            "value" => $ms_unit->unit_name,
            "required" => "true"
        ])->render("group");
    !!}
    {!! 
        Widget::select2("ka_unit",[
            "data" => [
                "model"     => "Employee",
                "filter"    => ["emp_active" => "t"],
                "column"    => ["emp_id","emp_name"]
            ],
            "selected"  => $ms_unit->ka_unit
        ])->render("group","Kepala Unit")
    !!}
    {!! 
        Create::dropDown("is_active",[
            "data" => [
                ["t" => "Aktif"],
                ["f" => "Non Aktif"]
            ],
            "selected"  => $ms_unit->is_active
        ])->render("group")
    !!}
    {!! 
        Create::dropDown("resiko_infeksi",[
            "data" => [
                "model"     => "Detail_indikator",
                "filter"    => ["indikator_id" => 5],
                "column"    => ["detail_id","detail_name"]
            ],
            "selected"  => $ms_unit->resiko_infeksi
        ])->render("group")
    !!}
    {!! 
        Create::dropDown("resiko_admin",[
            "data" => [
                "model"     => "Detail_indikator",
                "filter"    => ["indikator_id" => 6],
                "column"    => ["detail_id","detail_name"]
            ],
            "selected"  => $ms_unit->resiko_admin
        ])->render("group")
    !!}
    {!! 
        Create::dropDown("emergency_id",[
            "data" => [
                "model"     => "Detail_indikator",
                "filter"    => ["indikator_id" => 7],
                "column"    => ["detail_id","detail_name"]
            ],
            "selected"  => $ms_unit->emergency_id
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
        $('#form_ms_unit').parsley().on('field:validated', function() {
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
                            'data': $('#form_ms_unit').serialize(),
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