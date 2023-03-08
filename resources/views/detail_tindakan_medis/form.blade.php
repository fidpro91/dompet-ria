<?php
use \fidpro\builder\Create;
?>
    {!! Form::open(['route' => 'detail_tindakan_medis.store','id'=>'form_detail_tindakan_medis']) !!}
    <div class="card-body">
        {!! Form::hidden('tindakan_id', $detail_tindakan_medis->tindakan_id, array('id' => 'tindakan_id')) !!}
        {!! Create::input("jp_medis_id",[
                    "value"     => $detail_tindakan_medis->jp_medis_id,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("tanggal_tindakan",[
                    "value"     => $detail_tindakan_medis->tanggal_tindakan,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("nama_tindakan",[
                    "value"     => $detail_tindakan_medis->nama_tindakan,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("tarif_tindakan",[
                    "value"     => $detail_tindakan_medis->tarif_tindakan,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("id_klasifikasi_jasa",[
                    "value"     => $detail_tindakan_medis->id_klasifikasi_jasa,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("klasifikasi_jasa",[
                    "value"     => $detail_tindakan_medis->klasifikasi_jasa,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("percentase_jasa",[
                    "value"     => $detail_tindakan_medis->percentase_jasa,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("skor_jasa",[
                    "value"     => $detail_tindakan_medis->skor_jasa,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("qty_tindakan",[
                    "value"     => $detail_tindakan_medis->qty_tindakan,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("px_norm",[
                    "value"     => $detail_tindakan_medis->px_norm,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("px_name",[
                    "value"     => $detail_tindakan_medis->px_name,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("unit_layanan",[
                    "value"     => $detail_tindakan_medis->unit_layanan,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("unit_layanan_id",[
                    "value"     => $detail_tindakan_medis->unit_layanan_id,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("visit_id",[
                    "value"     => $detail_tindakan_medis->visit_id,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("nip",[
                    "value"     => $detail_tindakan_medis->nip,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("nama_dokter",[
                    "value"     => $detail_tindakan_medis->nama_dokter,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("unit_vip",[
                    "value"     => $detail_tindakan_medis->unit_vip,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("penjamin_id",[
                    "value"     => $detail_tindakan_medis->penjamin_id,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("nama_penjamin",[
                    "value"     => $detail_tindakan_medis->nama_penjamin,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("status_bayar",[
                    "value"     => $detail_tindakan_medis->status_bayar,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("tanggal_import",[
                    "value"     => $detail_tindakan_medis->tanggal_import,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("billing_id",[
                    "value"     => $detail_tindakan_medis->billing_id,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("status_jasa",[
                    "value"     => $detail_tindakan_medis->status_jasa,
                    
                    ])->render("group"); 
                !!}
{!! Create::input("jasa_tindakan_bulan",[
                    "value"     => $detail_tindakan_medis->jasa_tindakan_bulan,
                    
                    ])->render("group"); 
                !!}
    </div>
    <div class="card-footer text-center">
        {!! Form::submit('Save',['class' => 'btn btn-success']); !!}
        {!! Form::button('Cancel',['class' => 'btn btn-warning btn-refresh']); !!}
    </div>
    {!!Form::close()!!}

<script>
    $(document).ready(()=>{
        $('#form_detail_tindakan_medis').parsley().on('field:validated', function() {
            var ok = $('.parsley-error').length === 0;
            $('.bs-callout-info').toggleClass('hidden', !ok);
            $('.bs-callout-warning').toggleClass('hidden', ok);
        })
        .on('form:submit', function() {
            Swal.fire({
                title: 'Simpan Data?',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        'data': $('#form_detail_tindakan_medis').serialize(),
                        'dataType': 'json',
                        'success': function(data) {
                            Swal.fire("Sukses!", data.message, "success").then(() => {
                                location.reload();
                            });
                        }
                    });
                }
            })
            return false;
        });
    })
</script>