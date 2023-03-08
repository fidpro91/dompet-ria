<div class="table-responsive">
    <table class="table table_potongan">
        <thead>
            <tr>
                <th>NO</th>
                <th>NIP</th>
                <th>NAMA</th>
                <th>GOLONGAN</th>
                <th>PERCENTASE POTONGAN</th>
                <th>POTONGAN</th>
                <th>#</th>
            </tr>
        </thead>
        <tbody>
            @foreach($potongan as $x => $v)
                <tr>
                    <td>{{$x+1}}</td>
                    <td>{{$v->emp_no}}</td>
                    <td>{{$v->emp_name}}</td>
                    <td>{{$v->golongan}}</td>
                    <td>
                        <input type="text" class="form-control input-sm money" name="percentase_pajak" value="{{$v->percentase_pajak}}">    
                    </td>
                    <td>
                        <input type="text" class="form-control input-sm money" name="potongan_value" value="{{$v->potongan_value}}">       
                    </td>
                    <td>
                        <button class="btn btn-xs btn-primary" onclick="update_pajak(this,'{{$v->potongan_id}}')"><i class="fas fa-check"></i></button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>
    $(document).ready(()=>{
        $(".table_potongan").DataTable({
            ordering: false,
            "drawCallback": function( settings ) {
                $('.money').inputmask("IDR");
            }
        });
    })
</script>