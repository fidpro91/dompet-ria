@extends('templates.mobile.layout2')
@section('content')
<div class="container">
    <h4>Detail Pembagian Jasa Pelayanan</h4>
</div>
<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="accordion accordion-flush accordion-style-two" id="accordionStyle2">
                <!-- Single Accordion -->
                <div class="accordion-item">
                    <div class="accordion-header" id="accordionFour">
                        <h6 data-bs-toggle="collapse" data-bs-target="#accordionStyleFour" aria-expanded="true" aria-controls="accordionStyleFour"><i class="bi bi-plus-lg"></i>Rangkuman Jasa Pelayanan</h6>
                    </div>
                    <div class="accordion-collapse collapse show" id="accordionStyleFour" aria-labelledby="accordionFour" data-bs-parent="#accordionStyle2">
                        <div class="accordion-body">
                            <table class="table">
                                <tr>
                                    <th colspan="2">Jasa Brutto :</th>
                                </tr>
                                <?php
                                $totalBrutto = 0;
                                foreach ($jasaBrutto as $key => $value) {
                                    echo "
                                    <tr>
                                        <td>$value->nama_komponen</td>
                                        <td class=\"pull-right\">" . convert_currency2($value->total_brutto) . "</td>
                                    </tr>";
                                    $totalBrutto += $value->total_brutto;
                                }
                                ?>
                                <tr>
                                    <th>Total Brutto :</th>
                                    <th class="pull-right"><?= convert_currency2($totalBrutto) ?></th>
                                </tr>
                                <tr>
                                    <th colspan="2">Potongan Jasa :</th>
                                </tr>
                                <?php
                                $totalPotongan = 0;
                                foreach ($potonganJasa as $key => $value) {
                                    echo "
                                    <tr>
                                        <td>$value->potongan_nama</td>
                                        <td class=\"pull-right\">" . convert_currency2($value->potongan_value) . "</td>
                                    </tr>";
                                    $totalPotongan += $value->potongan_value;
                                }
                                ?>
                                <tr>
                                    <th>Total Potongan :</th>
                                    <th class="pull-right"><?= convert_currency2($totalPotongan) ?></th>
                                </tr>
                                <tr>
                                    <th>Take Home Pay :</th>
                                    <th class="pull-right"><?= convert_currency2($totalBrutto - $totalPotongan) ?></th>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Single Accordion -->
                <div class="accordion-item">
                    <div class="accordion-header" id="accordionFive" onclick="load_home(this)">
                        <h6 class="collapsed" data-bs-toggle="collapse" data-bs-target="#accordionStyleFive" aria-expanded="false" aria-controls="accordionStyleFive"><i class="bi bi-plus-lg"></i>
                        Skoring Individu Pegawai
                    </h6>
                    </div>
                    <div class="accordion-collapse collapse" id="accordionStyleFive" aria-labelledby="accordionFive" data-bs-parent="#accordionStyle2">
                        <div class="accordion-body" id="skoringIndividu">
                        </div>
                    </div>
                </div>
                @if(Session::get('sesLogin')->is_medis == 't')
                <!-- Single Accordion -->
                <div class="accordion-item">
                    <div class="accordion-header" id="accordionSix" onclick="load_point(9,'point_eksekutif')">
                        <h6 class="collapsed" data-bs-toggle="collapse" data-bs-target="#accordionStyleSix" aria-expanded="false" aria-controls="accordionStyleSix"><i class="bi bi-plus-lg"></i>Point Pelayanan Medis</h6>
                    </div>
                    <div class="accordion-collapse collapse" id="accordionStyleSix" aria-labelledby="accordionSix" data-bs-parent="#accordionStyle2">
                        <div class="accordion-body" id="point_eksekutif">
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@push('js')
<script>
    function load_home (e) {
        // (e || window.event).preventDefault();
        fetch("{{url('mobile/jasa_pelayanan/skoring')}}" /*, options */)
        .then((response) => response.text())
        .then((html) => {
            document.getElementById("skoringIndividu").innerHTML = html;
        })
        .catch((error) => {
            console.warn(error);
        });
    }
    function load_point (id,page) {
        // (e || window.event).preventDefault();
        fetch("{{url('mobile/jasa_pelayanan/point_medis')}}/"+id /*, options */)
        .then((response) => response.text())
        .then((html) => {
            document.getElementById(page).innerHTML = html;
        })
        .catch((error) => {
            console.warn(error);
        });
    }
</script>
@endpush
@endsection