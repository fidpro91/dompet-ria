@extends('templates.layout')
@section('content')
<?php
use App\Models\Ms_reff;
use \fidpro\builder\Bootstrap;
?>
<div class="card border-0 shadow rounded" id="page_group_refference">
    <div class="card-body">
    <?=
            Bootstrap::tabs([
                "tabs"  => function(){
                    $data = Ms_reff::where("reffcat_id",'3')->get();
                    $tabs = [];
                    foreach ($data as $key => $value) {
                        $tabs[$value->reff_name] = [
                            "href"      => "link_".$value->reff_id,
                            "url"       => "performa_index/data/".$value->reff_id
                        ];
                    }
                    return $tabs;
                }
            ]);
        ?>
    </div>
</div>
<script>
    $(document).ready(()=>{
        $(".nav-link.active").trigger('click');
    })
</script>
@endsection