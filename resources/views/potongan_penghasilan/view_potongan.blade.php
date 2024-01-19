@extends('templates.layoutNoHeader')
@section('content')
<div class="card border-0 shadow rounded" id="page_potongan_penghasilan">
    <div class="card-body">
        <div class="table-responsive">
            <?=$table?>
        </div>
    </div>
</div>
<script>
    $(document).ready(()=>{
        $(".table").DataTable({
            dom: 'Bfrtip', // Menentukan elemen-elemen yang ingin ditampilkan
            buttons: [
                'csv', 'excel', 'pdf', 'print' // Menambahkan tombol untuk mendownload data
            ],
            "paging": false
        });
    })
</script>
@endSection