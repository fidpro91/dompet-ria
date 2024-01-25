<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Data_simrs extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'tindakan_id';
    protected $table = 'data_simrs';
    protected $fillable = [
        'tindakan_id',
        'tanggal_tindakan',
        'nama_tindakan',
        'tarif_tindakan',
        'id_klasifikasi_jasa',
        'klasifikasi_jasa',
        'percentase_jasa',
        'skor_jasa',
        'qty_tindakan',
        'px_norm',
        'px_name',
        'unit_layanan',
        'unit_layanan_id',
        'visit_id',
        'nip',
        'nama_dokter',
        'unit_vip',
        'penjamin_id',
        'nama_penjamin',
        'status_bayar',
        'billing_id',
        'jenis_tagihan',
        'repo_id'
    ];
}
