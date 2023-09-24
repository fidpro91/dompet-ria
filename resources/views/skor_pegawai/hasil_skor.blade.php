@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Create;
use \fidpro\builder\Bootstrap;
use \fidpro\builder\Widget;
use Illuminate\Support\Facades\DB;
$totalSkor = count(Cache::get('skorPegawai'));
$totalError = count(Cache::get('skorError'));
?>
<div class="row">
    <div class="col-xl-6 col-md-6">
        <div class="card-box widget-user">
            <div class="text-center">
                <h2 class="font-weight-normal text-primary" data-plugin="counterup"><?=$totalSkor?></h2>
                <h5>Skor Download</h5>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-md-6">
        <div class="card-box widget-user">
            <div class="text-center">
                <a href="#" onclick="view_error('1','Pegawai Tidak Ada Skor')">
                    <h2 class="font-weight-normal text-warning" data-plugin="counterup">
                        <?=$totalError?>
                    </h2>
                </a>
                <h5>Pegawai Tidak Ada SKor</h5>
            </div>
        </div>
    </div>
    <div class="col-xl-12 text-center">
        {!! Form::button('Simpan Download',['class' => 'btn btn-success btn-save']); !!}
        {!! Form::button('Batal Download',['class' => 'btn btn-warning','onclick'=>'history.back()']); !!}
    </div>
</div>
<div class="clear"></div>
<div class="row">
    <div class="col-md-12">
        <div class="card-box">
            <h4 class="card-title">HASIL GENEATOR SKOR INDIVIDU PEGAWAI</h4>
            <div class="table-responsive">
                <table class="table table-data">
                    <thead>
                        <tr>
                            <th rowspan="2">NO</th>
                            <th rowspan="2">NIP</th>
                            <th rowspan="2">NAMA</th>
                            <th rowspan="2">UNIT KERJA</th>
                            <?php
                                $data = Cache::get('skorPegawai');
                                $dataH = array_keys($data[0]['dataSkor']);
                                foreach ($dataH as $key => $value) {
                                    echo "<th colspan=\"2\">".strtoupper($value)."</th>";
                                }
                            ?>
                            <th rowspan="2">TOTAL SKOR</th>
                        </tr>
                        <tr>
                            <?php
                                foreach ($dataH as $key => $value) {
                                    echo "<th>SKOR</th><th>DETAIL</th>";
                                }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $row='';
                            foreach ($data as $key => $value) {
                                $row .= '
                                <tr>
                                    <td>'.($key+1).'</td>
                                    <td>'.$value['nip'].'</td>
                                    <td>'.$value['nama'].'</td>
                                    <td>'.$value['unit_kerja'].'</td>';
                                foreach ($value['dataSkor'] as $x => $res) {
                                    $row .= '
                                        <td>'.$res['skor'].'</td>
                                        <td>'.$res['keterangan'].'</td>
                                    ';
                                }
                                $row .= '<td>'.$value['totalSkor'].'</td>';
                                $row .= '</tr>';
                            }
                            echo $row;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
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
        $(".table-data").DataTable();
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
                        'type'    : 'post',
                        'beforeSend' : function(){
                            showLoading();
                        },
                        'url'     : '{{url("skor_pegawai/save_skor")}}',
                        'success': function(data) {
                            if (data.success) {
                                Swal.fire("Sukses!", data.message, "success").then(() => {
                                    location.href= "{{route('skor_pegawai.index')}}";
                                });
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
        $(".modal-body").load("{{URL('skor_pegawai/error_skor')}}");
    }
</script>
@endsection