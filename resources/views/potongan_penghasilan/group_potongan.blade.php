@extends('templates.layout')
@section('content')
<?php

use App\Models\Kategori_potongan;
use \fidpro\builder\Bootstrap;
?>
<div class="col-md-12">
    <div class="bg-picture card-box">
        <div class="profile-info-name">
            <img src="{{asset('assets/images/iconmoney.gif')}}" class="rounded-circle avatar-xl img-thumbnail float-left mr-3" alt="profile-image">
            <div class="profile-info-detail overflow-hidden">
                <h4 class="float-right">Total Jasa Pelayanan : {{convert_currency2($pencairan->total_nominal)}}</h4>
                {!! Form::hidden('id_cair', $pencairan->id_cair_header, array('id' => 'id_cair')) !!}
                <h4 class="m-0">{{$pencairan->no_pencairan}}</h4>
                <p class="text-muted"><i>{{$pencairan->keterangan}}</i></p>
                <p class="font-13">
                    {{
                        Bootstrap::DataTable("table-data_jaspel",[
                            "class" => "table table-hover"
                        ],[
                            "url"   => "jasa_pelayanan/get_dataTable",
                            "filter"    => [
                                "id_cair"   => $pencairan->id_cair_header
                            ],
                            "raw"   => [
                                'no_jasa',
                                'penjamin',
                                'nominal_pendapatan' => [
                                    "data"      => "nominal_pendapatan",
                                    "settings"  => [
                                        "render"    => "$.fn.dataTable.render.number( ',', '.', 2)"
                                    ]
                                ],
                                'percentase_jaspel',
                                'nominal_jaspel'     => [
                                    "data"      => "nominal_jaspel",
                                    "settings"  => [
                                        "render"    => "$.fn.dataTable.render.number( ',', '.', 2)"
                                    ]
                                ]
                            ],
                            "dataTable" => [
                                "autoWidth"     => "false",
                                "paging"        => "false",
                                "searching"     => "false"
                            ]
                        ])
                    }}
                </p>

                <ul class="social-list list-inline mt-3 mb-0">
                    <li class="list-inline-item">
                        <a href="javascript: void(0);" onclick="get_pdf_report()" class="social-list-item border-warning text-warning"><i class="mdi mdi-file-pdf"></i></a>
                    </li>
                    <li class="list-inline-item">
                        <a href="javascript: void(0);" class="social-list-item border-success text-success"><i class="mdi mdi-file-excel"></i></a>
                    </li>
                    <li class="list-inline-item">
                        <a href="javascript: void(0);" class="social-list-item border-danger text-danger"><i class="mdi mdi-share-variant"></i></a>
                    </li>
                </ul>

            </div>

            <div class="clearfix"></div>
        </div>
    </div>
</div>
<div class="col-md-12">
    <div class="card border-0 shadow rounded" id="page_group_refference">
        <div class="card-body">
            <?=
            Bootstrap::tabs([
                "tabs"  => function () {
                    $data = Kategori_potongan::where("potongan_active", 't')->get();
                    $tabs = [];
                    foreach ($data as $key => $value) {
                        $tabs[$value->nama_kategori] = [
                            "href"      => "link_" . $value->kategori_potongan_id,
                            "url"       => "potongan_penghasilan/data/" . $value->kategori_potongan_id
                        ];
                    }
                    return $tabs;
                }
            ]);
            ?>
        </div>
    </div>
</div>
<script>
    $(document).ready(() => {
        $(".nav-link.active").trigger('click');
    })

    function show_potongan(id) {
        var url = '{{url("potongan_penghasilan/show")}}/' + id;
        window.open(url, 'PopupWindow', 'width=800,height=600');
    }

    function get_pdf_report() {
        var id = $("#id_cair").val();
        var url = '{{url("pencairan_jasa_header/print/")}}/' + id;
        window.open(url, 'PopupWindow', 'width=800,height=600');
    }
</script>
@endsection