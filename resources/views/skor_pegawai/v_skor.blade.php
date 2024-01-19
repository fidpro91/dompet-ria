<div class="card-body">
    <div class="form-group">
        <div class="float-right btn-generate1">
            <button class="btn btn-warning" onclick="generate_skor(1)">Generate Skor</button>
        </div>
        <label for="">Skor Sertifikat Pegawai</label>
    </div>
    <div class="form-group">
        <div class="float-right btn-generate2">
            <button class="btn btn-warning" onclick="generate_skor(2)">Generate Skor</button>
        </div>
        <label for="">Skor Tugas Tambahan</label>
    </div>
    <div class="form-group">
        <div class="float-right btn-generate3">
            <button class="btn btn-warning" onclick="generate_skor(3)">Generate Skor</button>
        </div>
        <label for="">Skor Performa Index</label>
    </div>
    <button class="btn btn-info btn-block btn-finish">Selesai</button>
</div>
<script>
    function generate_skor(id) {
        $.get("{{url('skor_pegawai/set_skor')}}/"+id,function(resp){
            if (resp.code == 200) {
                $(".btn-generate"+id).html('<span class="badge badge-success">'+resp.message+'</span>');
            }else{
                Swal.fire("Oopss...!!", data.message, "error");
            }
        },'json')
    }
    $(document).ready(()=>{
        $(".btn-finish").click(()=>{
            location.href = "{{url('skor_pegawai/hasil_skor')}}";
        })
    })
</script>