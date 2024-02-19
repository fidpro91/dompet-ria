<?php
use fidpro\builder\Bootstrap;
?>
<div class="table-responsive">
    {!!
        Bootstrap::tableData($skoring,[
            "class" => "table"
        ]);
    !!}
</div>