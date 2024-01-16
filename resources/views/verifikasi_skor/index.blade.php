@extends('templates.layout')
@section('content')
<?php
use fidpro\builder\Create;
$getHeader = new ArrayIterator($skorPegawai);
$header = json_decode($getHeader->current()->detail);

?>
<div class="card border-0 shadow rounded" id="page_potongan_statis">
    <div class="card-header">
        {!!
            Form::button("Tambah",[
                "class" => "btn btn-primary add-form",
                "data-target" => "page_potongan_statis",
                "data-url" => route("potongan_statis.create")
            ])
        !!}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th rowspan="2">#</th>
                        <th rowspan="2">NO</th>
                        <th rowspan="2">NIP</th>
                        <th rowspan="2">NAMA</th>
                        <th rowspan="2">UNIT KERJA</th>
                        <?php
                        foreach ($header as $key => $value) {
                            echo "<th colspan=\"2\">" . strtoupper($value->kode) . "</th>";
                        }
                        ?>
                        <th rowspan="2">TOTAL SKOR</th>
                    </tr>
                    <tr>
                        <?php
                        foreach ($header as $key => $value) {
                            echo "<th>SKOR</th><th>DETAIL</th>";
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $row = '';
                    $totalSkor = 0;
                    foreach ($skorPegawai as $key => $value) {
                        $button = Create::action("<i class=\"fas fa-check\"></i>", [
                            "class"     => "btn btn-success btn-xs",
                            "onclick"   => "konfirmasi_skor($value->id)",
                        ]);
            
                        $button .= Create::action("<i class=\"mdi mdi-wechat\"></i>", [
                            "class"     => "btn btn-secondary btn-xs",
                            "onclick"   => "keluhan_skor($value->id)",
                        ]);
                        $row .= '
                    <tr>
                        <td>'.$button.'</td>
                        <td>' . ($key + 1) . '</td>
                        <td>' . $value->emp_no . '</td>
                        <td>' . $value->emp_name . '</td>
                        <td>' . $value->unit_name . '</td>';
                        foreach ($header as $x => $res) {
                            $detail = json_decode($value->detail, true);
                            $kode = $res->kode;
                            $detail = array_filter($detail, function ($var) use ($kode) {
                                return $var["kode"] == $kode;
                            });
                            $detail = array_values($detail);
                            $row .= '
                            <td class="center">' . ($detail[0]['skor'] ?? 0) . '</td>
                            <td>' . ($detail[0]['keterangan'] ?? "") . '</td>
                        ';
                        }
                        $row .= '<td>' . $value->total_skor . '</td>';
                        $row .= '</tr>';
                        $totalSkor += $value->total_skor;
                    }
                    echo $row;
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td colspan="19">TOTAL SKOR</td>
                        <td><?= $totalSkor ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
    function konfirmasi_skor(id) {
        Swal.fire({
            title: 'Anda yakin konfirmasi skor bulanan?',
            text: 'Konfirmasi skor tidak dapat dibatalkan. Skor otomatis terkirim untuk perhitungan remunerasi.',
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.value) {
                $.get('{{url("verifikasi_skor/konfirmasi_skor")}}/'+id,function(resp){
                    resp.success(response.message, "Message : ");
                    location.reload();
                },'json');
            }
        })
    }

    function keluhan_skor(id) {
        Swal.mixin({
            input: 'textarea',
            confirmButtonText: 'Kirim',
            showCancelButton: true,
            cancelButtonText: 'Batal',
        }).queue([
            {
                title: 'Form Keluhan Skor Pegawai',
                text: 'Masukkan detail keluhan skor individu pegawai :',
            },
        ]).then((result) => {
            if (result.value) {
                // Proses teks yang dimasukkan oleh pengguna
                const alasan = result.value[0];
                $.ajax({
                    'data': {
                        alasan: alasan,
                        id  : id
                    },
                    headers: {
                        'X-CSRF-TOKEN': '<?=csrf_token()?>'
                    },
                    'dataType': 'json',
                    'url'        : '{{url("verifikasi_skor/save_keluhan")}}',
                    'type'       : 'post',
                    'success': function(data) {
                        if (data.code == 200) {
                            Swal.fire("Sukses!", data.message, "success").then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire("Oopss...!!", data.message, "error");
                        }
                    }
                });
            }
        });
    }
</script>
@endsection