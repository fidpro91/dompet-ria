@extends('templates.layoutNoHeader')
@section('content')
<div class="alert alert-success">
    <strong>Well done!</strong> You successfully read this important alert message.
</div>
<div class="bg-picture card-box text-center">
    <a href="{{url('pengajuan_diklat/form_pengajuan')}}" class="btn btn-purple">Tambah Sertifikat</a>
    <a href="{{url('pengajuan_diklat')}}" class="btn btn-success">Selesai</a>
</div>
@endsection