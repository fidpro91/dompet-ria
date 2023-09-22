<div class="row">
    <div class="col-md-4">
        <span>TOTAL SKOR :</span>
    </div>
    <div class="col-md-8 pull-right">
        <span></span>
    </div>
    <div class="col-md-4">
        <span>TOTAL JASA BRUTTO :</span>
    </div>
    <div class="col-md-8 pull-right">
        <span></span>
    </div>
    <div class="col-md-12">
        {!! 
            Bootstrap::tableData($dataJasa,["class"=>"table table-bordered table-detail"]);
        !!}
    </div>
</div>