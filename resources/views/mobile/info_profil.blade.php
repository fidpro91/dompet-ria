@extends('templates.mobile.layout2')
@section('content')
<div class="container">
    <div class="card user-info-card mb-3">
        <div class="card-body d-flex align-items-center">
            <div class="user-profile me-3"><img src="{{asset('storage/uploads/photo_pegawai/'.Session::get('sesLogin')->photo)}}" alt="">
            </div>
            <div class="user-info">
                <div class="d-flex align-items-center">
                    <h5 class="mb-1">{{Session::get('sesLogin')->emp_name}}</h5>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="accordion accordion-flush accordion-style-one" id="accordionStyle1">
              <!-- Single Accordion -->
              <div class="accordion-item">
                <div class="accordion-header" id="accordionOne">
                  <h6 data-bs-toggle="collapse" data-bs-target="#accordionStyleOne" aria-expanded="true" aria-controls="accordionStyleOne">Biodata Kepegawaian<i class="bi bi-chevron-down"></i></h6>
                </div>
                <div class="accordion-collapse collapse show" id="accordionStyleOne" aria-labelledby="accordionOne" data-bs-parent="#accordionStyle1">
                  <div class="accordion-body">
                    <table class="table">
                        <tr>
                            <td>Unit Kerja</td>
                            <td>{{Session::get('sesLogin')->unit_name}}</td>
                        </tr>
                        <tr>
                            <td>Jabatan Struktural</td>
                            <td>{{Session::get('sesLogin')->jabatan_struktural_name}}</td>
                        </tr>
                        <tr>
                            <td>Jabatan Fungsional</td>
                            <td>{{Session::get('sesLogin')->jabatan_fungsional_name}}</td>
                        </tr>
                        <tr>
                            <td>Golongan</td>
                            <td>{{Session::get('sesLogin')->golongan}}</td>
                        </tr>
                        <tr>
                            <td>Gaji Pokok</td>
                            <td>{{convert_currency2(Session::get('sesLogin')->gaji_pokok)}}</td>
                        </tr>
                        <tr>
                            <td>Tunjangan Profesi</td>
                            <td>{{convert_currency2(Session::get('sesLogin')->gaji_add)}}</td>
                        </tr>
                        <tr>
                            <td>PTKP</td>
                            <td>{{Session::get('sesLogin')->nama_potongan}}</td>
                        </tr>
                        <tr>
                            <td>Nomor Rekening</td>
                            <td>{{Session::get('sesLogin')->nomor_rekening}}</td>
                        </tr>
                    </table>
                  </div>
                </div>
              </div>
              <!-- Single Accordion -->
              <div class="accordion-item">
                <div class="accordion-header" id="accordionTwo" onclick="load_info(1,'listSertifikat')">
                  <h6 class="collapsed" data-bs-toggle="collapse" data-bs-target="#accordionStyleTwo" aria-expanded="false" aria-controls="accordionStyleTwo">Sertifikasi<i class="bi bi-chevron-down"></i></h6>
                </div>
                <div class="accordion-collapse collapse" id="accordionStyleTwo" aria-labelledby="accordionTwo" data-bs-parent="#accordionStyle1">
                  <div class="accordion-body">
                    <div class="table-responsive" id="listSertifikat"></div>
                  </div>
                </div>
              </div>
              <!-- Single Accordion -->
              <div class="accordion-item">
                <div class="accordion-header" id="accordionThree" onclick="load_info(2,'listTT')">
                  <h6 class="collapsed" data-bs-toggle="collapse" data-bs-target="#accordionStyleThree" aria-expanded="false" aria-controls="accordionStyleThree">Tugas Tambahan<i class="bi bi-chevron-down"></i></h6>
                </div>
                <div class="accordion-collapse collapse" id="accordionStyleThree" aria-labelledby="accordionThree" data-bs-parent="#accordionStyle1">
                  <div class="accordion-body">
                    <div class="table-responsive" id="listTT">
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>
    </div>
</div>
@push('js')
<script>
    function load_info (id,page) {
        fetch("{{url('mobile/profil/info')}}/"+id /*, options */)
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