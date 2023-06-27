@extends('templates.mobile.layout2')
@section('content')
<?php
use Illuminate\Support\Facades\DB;
?>
<div class="container">
    @foreach($data['pencairan'] as $rs)
        <div class="card timeline-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="timeline-text mb-2">
                        <span class="badge mb-2 rounded-pill">
                            {{date_indo2($rs->tanggal_cair)}}
                        </span>
                        <h6>{{$rs->keterangan}}</h6>
                    </div>
                </div>
                <div class="timeline-tags">
                    <?php
                        $penjamin = DB::table('persentase_jasa')->where("id_cair",$rs->id_cair_header)->pluck('penjamin');
                    ?>
                    @foreach($penjamin as $pj)
                        <span class="badge bg-light text-dark">#{{$pj}}</span>
                    @endforeach
                </div>
                <a class="btn m-1 btn-sm btn-success" href="{{url('mobile/jasa_pelayanan/detail/'.$rs->id_cair_header.'')}}">LIHAT DETAIL</a>
            </div>
        </div>
    @endforeach
</div>
<div class="container">
    <div class="card">
        <div class="card-body text-end mb-0">
            {{ $data['pencairan']->links('vendor.pagination.simple-bootstrap-4') }}
        </div>
    </div>
</div>
@endsection