<?php
use \fidpro\builder\Create;
?>
    {!! Form::open(['route' => 'activity_log.store','id'=>'form_activity_log']) !!}
    <div class="card-body">
        {!! Form::hidden('id', $activity_log->id, array('id' => 'id')) !!}
        {!! Create::input("log_name",[
                    "value"     => $activity_log->log_name,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("description",[
                    "value"     => $activity_log->description,
                    "required"  => "true"
                    ])->render("group"); 
                !!}
{!! Create::input("subject_type",[
                    "value"     => $activity_log->subject_type,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("subject_id",[
                    "value"     => $activity_log->subject_id,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("causer_type",[
                    "value"     => $activity_log->causer_type,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("causer_id",[
                    "value"     => $activity_log->causer_id,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("properties",[
                    "value"     => $activity_log->properties,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("created_at",[
                    "value"     => $activity_log->created_at,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("updated_at",[
                    "value"     => $activity_log->updated_at,
                    
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
        $('#form_activity_log').parsley().on('field:validated', function() {
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
                        'data': $('#form_activity_log').serialize(),
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