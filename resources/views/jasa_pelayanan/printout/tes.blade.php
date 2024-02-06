<h1 align="center">
    <b>LAPORAN REKAPITULASI  <?=$data['header'][0]->keterangan?><br>
    RSUD IBNU SINA KABUPATEN GRESIK</b><br>
    <small>Bulan <?=get_namaBulan($data['header'][0]->jaspel_bulan)?> Tahun <?=$data['header'][0]->jaspel_tahun?></small>
</h1>
<p></p>
<table>
    <thead>
        <tr>
            <td colspan="2">Total Pendapatan :</td>
            <td colspan="2"></td>
            <td class="angka"><?= convert_currency2($data['header'][0]->nominal_pendapatan) ?></td>
        </tr>
        <tr>
            <td colspan="2">Proporsi Jasa :</td>
            <td colspan="2" class="angka"><?= $data['header'][0]->percentase_jaspel ?>%</td>
            <td class="angka"><?= convert_currency2($data['header'][0]->nominal_jaspel) ?></td>
        </tr>
        <tr>
            <th>NO</th>
            <th>PROPORSI JASA</th>
            <th>PERCENTASE(%)</th>
            <th>NOMINAL(Rp)</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data['header'] as $key => $v)
        <tr>
            <td>{{($key+1)}}</td>
            <td>{{$v->komponen_nama}}</td>
            <td>{{$v->percentase}}</td>
            <td>{{$v->nominal}}</td>
            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>
<p></p>
    <h2 style="text-align: center; font-weight:bold">JASA PELAYANAN BY NAME</h2>
<p></p>
<table class="table" border="1">
    @foreach ($data['detail'] as $key => $value)
    @php ($detail = json_decode($value->detail))
    <tr>
        <th colspan='5' style='text-align:center'>{{$value->nama_komponen}}</th>
    </tr>
    <tr>
        <th>NO</th>
        <th>NIP</th>
        <th>NO REK</th>
        <th>NAMA</th>
        <th>SKOR</th>
        <th>NOMINAL</th>
    </tr>
    @php ($totalRow=0)
    @foreach ($detail as $x => $v)
    @php ($totalRow += $v->nominal);
    <tr>
        <td>{{($x+1)}}</td>
        <td>{{$v->nomor_rekening}}</td>
        <td>{{$v->nip}}</td>
        <td>{{$v->nama}}</td>
        <td>{{$v->skor}}</td>
        <td>{{convert_currency2($v->nominal)}}</td>
    </tr>
    @endforeach
    <tr>
                    <th></th>
                    <th colspan=\"3\">TOTAL</th>
                    <th>{{convert_currency2($totalRow)}}</th>
                </tr>
    @endforeach
</table>