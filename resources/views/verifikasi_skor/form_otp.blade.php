<?php
use fidpro\builder\Create;
use Illuminate\Support\Facades\Session;

$sess = Session::get('sesLogin');
?>
<div class="card-body" >
    <h4>Hii... {{$sess->emp_name}}</h4>
    <p>Silahkan masukkan kode OTP yang dikirim ke pesan whatsapp anda untuk melanjutkan prosess verifikasi skor.</p>
    {!! 
        Create::input("kode_otp",[
            "required"      => "true",
            "placeholder"   => "Masukkan kode OTP yang dikirim lewat whatsapp anda"
        ])->render("group","Kode OTP :");
    !!}
    <button class="btn btn-primary" onclick="verifikasi_otp()">Verifikasi</button>
</div>
<script>
    function verifikasi_otp() {
        $.ajax({
            'data': {
                kodeotp : $("#kode_otp").val(),
            },
            headers: {
                'X-CSRF-TOKEN': '<?=csrf_token()?>'
            },
            'beforeSend': function() {
                showLoading();
            },
            'type'    : 'post',
            'url'     : '{{url("verifikasi_skor/validasi_otp")}}',
            'dataType': 'json',
            'success': function(data) {
                if (data.code == 200) {
                    Swal.close();
                    window.location.assign(data.redirect);
                }else{
                    Swal.fire("Oopss...!!", data.message, "error");
                }
            }
        });
    }
</script>