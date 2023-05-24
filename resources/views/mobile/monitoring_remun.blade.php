@extends('templates.mobile.layout')
@section('content')
<div class="container">
  <div class="card position-relative shadow-sm">
    <div class="card-body direction-rtl">
      <h2>Monitoring Detail Remunerasi</h2>
      <p>Untuk melihat detail perhitungan jasa pelayanan by name. Silahkan akses link berikut melalui web browser anda kemudian login. Lalu klik tombol dibawah ini.</p><a class="btn btn-primary" href="{{url('rekap_jaspel')}}">Detail Remun</a>
    </div>
  </div>
</div>
@endsection