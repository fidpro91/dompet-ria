@extends('templates.layout')
@section('content')
<?php
use \fidpro\builder\Bootstrap;
use App\Models\Group_refference;
?>
<div class="card border-0 shadow rounded" id="page_group_refference">
    <div class="card-header">
        {!!
            Form::button("Group Baru",[
                "class" => "btn btn-purple add-form",
                "data-target" => "page_group_refference",
                "data-url" => route("group_refference.create")
            ])
        !!}
    </div>
    <div class="card-body">
    <?=
            Bootstrap::tabs([
                "tabs"  => function(){
                    $data = Group_refference::where("group_reff_active",'t')->get();
                    $tabs = [];
                    foreach ($data as $key => $value) {
                        $tabs[$value->group_reff] = [
                            "href"      => "link_".$value->id,
                            "url"       => "ms_reff/data/".$value->id
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