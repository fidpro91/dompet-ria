@extends('templates.layout')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card border-0 shadow rounded">
        <div class="card-header bg-primary">Statistik Pendapatan Jasa Pelayanan</div>
            <div class="card-body">
                {!! $chart['globalPendapatan']->container() !!}
            </div>
        </div>
        <div class="card border-0 shadow rounded">
            <div class="card-header bg-primary">Statistik Jasa Pelayanan Unit</div>
            <div class="card-body overflow-auto">
                <div style="min-width: 5000px !important;">
                    {!! $chart['statistik']->container() !!}
                </div>
            </div>
        </div>
    </div>
</div>
{!! $chart['statistik']->script() !!}
{!! $chart['globalPendapatan']->script() !!}
@endsection