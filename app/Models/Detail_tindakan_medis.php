<?php

namespace App\Models;

use App\Libraries\Servant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detail_tindakan_medis extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'tindakan_id';
    protected $table = 'detail_tindakan_medis';
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
        'visit_id',
        'nama_dokter',
        'unit_vip',
        'penjamin_id',
        'status_bayar',
        'billing_id',
        'jenis_tagihan',
        'repo_id',
        'id_dokter'
    ];

    public function get_data_surety(){
        $dt = Servant::connect_simrs('GET','get_penjamin');
		$dt = json_decode($dt);
		return $dt->response;
    }
}
