<?php
use \fidpro\builder\Bootstrap;
?>

<?=
    Bootstrap::tabs([
        "tabs"  => function () use($komponen) {
                $tabs=[];
                foreach ($komponen as $key => $value) {
                    $tabs[$value->nama_komponen] = [
                        "href"      => "tab_".$value->id,
                        "url"       => url("jp_byname_medis/get_data/".$value->id)
                    ];
                }
                return $tabs;
            }
    ]);
?>
<script>
    $(document).ready(()=>{
        $(".nav-link.active").trigger('click');
    })
</script>