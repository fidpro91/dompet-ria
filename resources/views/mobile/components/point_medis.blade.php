<?php
use fidpro\builder\Bootstrap;
?>
@foreach($pointMedis as $x=>$val)
<div class="accordion-item">
    <div class="accordion-header" id="accordion{{$x}}">
        <h6 class="collapsed" data-bs-toggle="collapse" data-bs-target="#accordionStyle{{$x}}" aria-expanded="false" aria-controls="accordionStyle{{$x}}"><i class="bi bi-plus-lg"></i>
        {{$val->nama_penjamin}} Periode {{date_indo2($val->periode_awal)}}/{{date_indo2($val->periode_akhir)}}
    </h6>
    </div>
    <div class="accordion-collapse collapse" id="accordionStyle{{$x}}" aria-labelledby="accordion{{$x}}" data-bs-parent="#accordionStyle1">
        <div class="accordion-body">
            <?php
                $detail = json_decode($val->detail,true);
                echo Bootstrap::tableData($detail,["class"=>"table"])
            ?>
        </div>
    </div>
</div>
@endforeach