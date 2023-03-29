<?php

use \fidpro\builder\Create;
use \fidpro\builder\Bootstrap;
?>
{!! Form::open(['route' => 'ms_group.store','id'=>'form_ms_group']) !!}
<div class="card-body">
    <div class="row">
        <div class="col-md-6">
            {!! Form::hidden('group_id', $ms_group->group_id, array('id' => 'group_id')) !!}
            {!! 
                Create::input("group_code",[
                    "value" => $ms_group->group_code
                ])->render("group");
            !!}
            {!! 
                Create::input("group_name",[
                    "value" => $ms_group->group_name,
                    "required" => "true"
                ])->render("group");
            !!}
            {!!
                Create::dropDown("group_type",[
                "data" => [
                    ["1" => "Web"],
                    ["2" => "Mobile"]
                ],
                "selected" => $ms_group->group_type,
                "extra" => [
                    "required" => true
                ]
                ])->render("group");
            !!}
            {!!
                Create::dropDown("group_active",[
                "data" => [
                    ["t" => "Aktif"],
                    ["f" => "Non Aktif"]
                ],
                "selected" => $ms_group->group_active,
                "extra" => [
                    "required" => true
                ]
                ])->render("group");
            !!}
        </div>
        <div class="col-md-6">
            {{
                Bootstrap::DataTable("tableMenu",[
                    "class" => "table table-hover"
                ],[
                    "url"       => "ms_group/get_hak_akses",
                    "filter"    => ["group_id" => "$('#group_id').val()"],
                    "raw"   => [
                        '#'     => [
                            "data" => "checkbox", 
                            "name" => "checkbox",
                            "settings" => [
                                "orderable" => "false", 
                                "searchable" => "false"
                            ]
                        ],
                        'menu_code','menu_name'
                    ],
                    "dataTable" => [
                        "paging"    => "false",
                        "ordering"  => "false",
                        "info"      => "false",
                    ]
                ])
            }}
        </div>
    </div>
</div>
<div class="card-footer text-center">
    {!! Form::submit('Save',['class' => 'btn btn-success']); !!}
    {!! Form::button('Cancel',['class' => 'btn btn-warning btn-refresh']); !!}
</div>
{!!Form::close()!!}

<script>
    $(document).ready(() => {
        $('#form_ms_group').parsley().on('field:validated', function() {
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
                            'data': $('#form_ms_group').serialize(),
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