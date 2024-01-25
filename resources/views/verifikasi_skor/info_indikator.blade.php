<?php

use App\Models\Indikator;

$data = Indikator::where("status","t")->select(["indikator","bobot","deskripsi"])->get();
$table = \fidpro\builder\Bootstrap::tableData($data,["class"=>"table table-bordered"]);

echo $table;
?>