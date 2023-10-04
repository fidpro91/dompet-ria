@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Create;
use \fidpro\builder\Bootstrap;
use \fidpro\builder\Widget;
use Illuminate\Support\Facades\DB;
// print_r(Cache::get('billCache'));
$totalDownload = count(Cache::get('billCache'));
$totalSkor     = count(Cache::get('skorCache'));
$errorDownload = Cache::get('errorDownloadCache');
$dokterFail    = count($errorDownload['dokterFail']);
$employeeOff  = count(Cache::get('employeeOffCache'));
?>
<div class="row">
    <div class="col-xl-4 col-md-6">
        <div class="card-box widget-user">
            <div class="text-center">
                <h2 class="font-weight-normal text-primary" data-plugin="counterup"><?=$totalDownload?></h2>
                <h5>Data Download</h5>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="card-box widget-user">
            <div class="text-center">
                <a href="#" onclick="view_error('2','Data Medis Tidak Terdaftar')">
                    <h2 class="font-weight-normal text-pink" data-plugin="counterup"><?=$dokterFail?></h2>
                    <h5>Data Medis Tidak Terdaftar</h5>
                </a>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="card-box widget-user">
            <div class="text-center">
                <h2 class="font-weight-normal text-warning" data-plugin="counterup">0</h2>
                <h5>Data Tindakan Tidak Terdaftar</h5>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-4 col-md-6">
        <div class="card-box widget-user">
            <div class="text-center">
                <h2 class="font-weight-normal text-primary" data-plugin="counterup"><?=$totalSkor?></h2>
                <h5>Skor Download</h5>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="card-box widget-user">
            <div class="text-center">
                <h2 class="font-weight-normal text-pink" data-plugin="counterup"><?=$totalSkor?></h2>
                <h5>Pegawai Ada Skor</h5>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="card-box widget-user">
            <div class="text-center">
                <a href="#" onclick="view_error('1','Pegawai Tidak Ada Skor')">
                    <h2 class="font-weight-normal text-warning" data-plugin="counterup">
                        <?=$employeeOff?>
                    </h2>
                </a>
                <h5>Pegawai Tidak Ada SKor</h5>
            </div>
        </div>
    </div>
    <div class="col-xl-12 text-center">
        {!! Form::button('Simpan Download',['class' => 'btn btn-success btn-save']); !!}
        {!! Form::button('Batal Download',['class' => 'btn btn-warning','onclick' => 'history.back()']); !!}
    </div>
</div>
{{
    Bootstrap::modal('modal_error',[
        "title"   => 'Detail Error  <span id="title-error"></span>',
        "size"    => "modal-xl",
        "body"    => [
                        "content"   => ''
                    ]
    ])
}}
<script>
    $(document).ready(()=>{
        $(".btn-save").click(()=>{
            Swal.fire({
                title: 'Simpan data download?',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        'dataType': 'json',
                        'headers': {
                            'X-CSRF-TOKEN': "<?=csrf_token()?>"
                        },
                        'beforeSend': function() {
                            showLoading();
                        },
                        'type'    : 'post',
                        'url'     : '{{route("detail_tindakan_medis.store")}}',
                        'success': function(data) {
                            if (data.success) {
                                Swal.close();
                                location.href= "{{route('detail_tindakan_medis.index')}}";
                            }else{
                                Swal.fire("Oopss..!!", data.message, "error")
                            }
                        }
                    });
                }
            })
        })
    })

    function view_error(a,b) {
        $("#modal_error").modal("show");
        $("#title-error").text(b);
        $(".modal-body").load("{{URL('detail_tindakan_medis/get_error')}}/"+a+"");
    }
</script>
@endsection