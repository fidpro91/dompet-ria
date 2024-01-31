@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Bootstrap;
use \fidpro\builder\Create;
if(!Cache::has('cacheJasaMerger')){
    $dataJasaEks = Cache::get('cacheEksekutif');
    $dataJNonmedis = Cache::get('cacheJasaProporsi');
    $jabar[] = $dataJasaEks; 
    $allJasa = array_merge($dataJNonmedis,$jabar);
    Cache::add("cacheJasaMerger",$allJasa,3000);
    Cache::forget("cacheEksekutif");
    Cache::forget("cacheJasaProporsi");
}
$input = Cache::get('cacheInputJasa');
$jasaHeader = Cache::get('cacheJasaHeader');
$allJasa=Cache::get("cacheJasaMerger");
?>
<style>
    th, td {
        padding: 2px !important;
        font-size: 15px !important;
    }
</style>
<div class="card border-0 shadow rounded">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <td colspan="2">Total Pendapatan :</td>
                    <td colspan="2"></td>
                    <td class="angka"><?= convert_currency2($input['nominal_pendapatan']) ?></td>
                </tr>
                <tr>
                    <td colspan="2">Proporsi Jasa :</td>
                    <td colspan="2" class="angka"><?=($input['percentase_jaspel']) ?>%</td>
                    <td class="angka"><?= convert_currency2($input['nominal_pembagian']) ?></td>
                </tr>
                <tr>
                    <th>NO</th>
                    <th>PROPORSI JASA</th>
                    <th>PERCENTASE(%)</th>
                    <th>NOMINAL(Rp)</th>
                    <th></th>
                </tr>
            </thead>
            <?php
                $header="";
                foreach ($jasaHeader as $key => $value) {
                    $header .= "
                        <tr>
                            <td>".($key+1)."</td>
                            <td>".$value['komponen']."</td>
                            <td>".$value['percentase']."%</td>
                            <td>".convert_currency2($value['nominal'])."</td>
                            <td></td>
                        </tr>
                    ";
                    if (isset($value['detail'])) {
                        foreach ($value['detail'] as $x => $c) {
                            $header .= "
                                <tr>
                                    <td></td>
                                    <td>".$c['komponen']."</td>
                                    <td>".$c['percentase']."%</td>
                                    <td></td>
                                    <td>".convert_currency2($c['nominal'])."</td>
                                </tr>
                            ";
                        }
                    }
                }
                echo $header;
            ?>
        </table>
    </div>
    <div class="card-footer text-center">
        {!! Form::button('Simpan Data Jasa',['class' => 'btn btn-success btn-save']); !!}
        {!! Form::button('Finish Data Jasa',['class' => 'btn btn-primary', 'onclick' => 'finish_jaspel()']); !!}
        {!! Form::button('Cancel',['class' => 'btn btn-warning', 'onclick' => 'delete_jaspel()']); !!}
    </div>
</div>
<div class="card border-0 shadow rounded">
    <div class="card-body">
        <?=
            Bootstrap::tabs([
                "tabs"  => function () use($allJasa) {
                        $tabs=[];
                        foreach ($allJasa as $key => $value) {
                            $dataJasa = $value['detail'] ?? null;
                            $button = Create::action("<i class=\"fas fa-check\"></i> Checkout", [
                                "class"     => "btn btn-info",
                                "onclick"   => "checkout_proporsi(".$value['komponen_id'].")"
                            ]);
                            $tabs[$value['komponen_nama']] = [
                                "href"      => "tab_".$value['komponen_id'],
                                "content"   => function() use($dataJasa,$value,$button){
                                    $html = '
                                    <div class="row">
                                        <div class="col-md-4">
                                            '.$button.'
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <span>TOTAL SKOR / PENERIMA :</span>
                                        </div>
                                        <div class="col-md-8 pull-right">
                                            <span>'.($value['total_skor']??'-').'</span>
                                        </div>
                                        <div class="col-md-4">
                                            <span>TOTAL JASA BRUTTO :</span>
                                        </div>
                                        <div class="col-md-8 pull-right">
                                            <span>'.(convert_currency2($value['total_jasa']??'0')).'</span>
                                        </div>
                                        <div class="col-md-12">
                                            '.
                                                Bootstrap::tableData($dataJasa,["class"=>"table table-bordered table-detail"])
                                            .'
                                        </div>
                                    </div>';
                                    return $html;
                                }
                            ];
                        }
                        return $tabs;
                    }
            ]);
        ?>
    </div>
</div>
<script>
    var jaspelId;
    $(document).ready(()=>{
        $(".table-detail").DataTable({
            paging: false,
            ordering: false,
            info: false,
            'columnDefs': [
                {
                    "targets": [-1,-2],
                    'render': $.fn.dataTable.render.number( ',', '.', 2)
                }
            ]
        });

        $(".btn-save").click(()=>{
            Swal.fire({
                title: 'Simpan Data Jasa Pelayanan?',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        'dataType': 'json',
                        'type'  : 'post',
                        'headers': {
                            'X-CSRF-TOKEN': "<?= csrf_token() ?>"
                        },
                        'beforeSend': function() {
                            showLoading();
                        },
                        'url'   : '{{route("jasa_pelayanan.store")}}',
                        'success': function(data) {
                            if (data.success) {
                                Swal.fire("Sukses!", data.message, "success").then(() => {
                                    jaspelId = data.response.jaspel_id;
                                    sessionStorage.setItem('jaspelId', data.response.jaspel_id);
                                });
                            }else{
                                Swal.fire("Oopss...!!", data.message, "error");
                            }
                        }
                    });
                }
                return false;
            })
        })
    })

    function checkout_proporsi(komponen_id) {
        jaspelId = sessionStorage.getItem('jaspelId');
        if (!jaspelId) {
            Swal.fire("Oopss...!!", "Data dasar jasa belum disimpan. Silahkan simpan data dasar jasa terlebih dahulu", "error");
            return false;
        }
        showLoading();
        $.get("{{url('jasa_pelayanan/simpan_per_proporsi')}}/"+jaspelId+"/"+komponen_id,function(resp){
            if (resp.code == 200) {
                Swal.fire({
                    title: "Sukses!",
                    text: resp.message,
                    type: "success",
                    timer: 1500,  // Waktu dalam milidetik sebelum SweetAlert ditutup otomatis
                });
            }else{
                Swal.fire("Oopss...!!", data.message, "error");
            }
        },'json')
    }

    function delete_jaspel() {
        Swal.fire({
                title: 'Batal perhitungan jasa pelayan?',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.value) {
                    showLoading();
                    $.get("{{url('jasa_pelayanan/remove_jaspel')}}/"+jaspelId,function(data){
                        if (data.success) {
                            Swal.fire("Sukses!", data.message, "success").then(() => {
                                location.reload();
                            });
                        }else{
                            Swal.fire("Oopss...!!", data.message, "error");
                        }
                    },'json');
                }
            })
    }

    function finish_jaspel() {
        jaspelId = sessionStorage.getItem('jaspelId');
        if (!jaspelId) {
            Swal.fire("Oopss...!!", "Data dasar jasa belum disimpan. Silahkan simpan data dasar jasa terlebih dahulu", "error");
            return false;
        }
        Swal.fire({
                title: 'Selesai perhitungan jasa pelayan?',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        'dataType': 'json',
                        'type'  : 'post',
                        'headers': {
                            'X-CSRF-TOKEN': "<?= csrf_token() ?>"
                        },
                        'data' : {
                            'jaspel_id' : jaspelId
                        },
                        'beforeSend': function() {
                            showLoading();
                        },
                        'url'   : '{{url("jasa_pelayanan/finish_jaspel")}}',
                        'success': function(data) {
                            if (data.code == 200) {
                                sessionStorage.removeItem('jaspelId');
                                Swal.fire("Sukses!", data.message, "success").then(() => {
                                    location.href = "{{route('jasa_pelayanan.index')}}"
                                });
                            }else{
                                Swal.fire("Oopss...!!", data.message, "error");
                            }
                        }
                    });
                }
            })
    }
</script>
@endsection