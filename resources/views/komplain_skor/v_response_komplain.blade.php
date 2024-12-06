{!! Form::open(['class'=>'form_komplain_skor']) !!}
{!! Form::hidden('id_komplain', $data->id_komplain) !!}
    <span>{{$data->isi_komplain}}</span>
    <hr>
    @if($data->status_komplain == 1)
    <div class="input-group">
        <textarea name="tanggapan_komplain" class="form-control tanggapan_komplain"></textarea>
        <button class="btn btn-primary" type="button" onclick="send_response(this)">Kirim</button>
    </div>
    @foreach($template as $temp)
        <a href="javascript:void(0)" onclick="set_temp(this)">
            <span class="badge badge-info badge-lg mt-1">{{$temp->reff_name}}</span>
        </a>
    @endforeach
    @endif
    @if($data->status_komplain == 2)
        <span><b>Jawaban : </b><br>{{$data->tanggapan_komplain}}</span>
    @endif
{!!Form::close()!!}