<?php
use \fidpro\builder\Create;
?>
    {!! Form::open(['route' => 'ms_menu.store','id'=>'form_ms_menu']) !!}
    <div class="card-body">
        {!! Form::hidden('menu_id', $ms_menu->menu_id, array('id' => 'menu_id')) !!}
        {!! Create::input("menu_code",[
                    "value"     => $ms_menu->menu_code,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("menu_name",[
                    "value"     => $ms_menu->menu_name,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("menu_url",[
                    "value"     => $ms_menu->menu_url,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("menu_parent_id",[
                    "value"     => $ms_menu->menu_parent_id,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("menu_status",[
                    "value"     => $ms_menu->menu_status,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("menu_icon",[
                    "value"     => $ms_menu->menu_icon,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("slug",[
                    "value"     => $ms_menu->slug,
                    
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
        $('#form_ms_menu').parsley().on('field:validated', function() {
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
                        'data': $('#form_ms_menu').serialize(),
                        'dataType': 'json',
                        'success': function(data) {
                            Swal.fire("Sukses!", data.message, "success").then(() => {
                                location.reload();
                            });
                        }
                    });
                }
            })
            return false;
        });
    })
</script>