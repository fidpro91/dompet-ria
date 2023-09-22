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
        'jp_medis_id',
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
        'jenis_tagihan',
        'nama_dokter',
        'unit_vip',
        'penjamin_id',
        'nama_penjamin',
        'status_bayar',
        'bulan_pelayanan',
        'tanggal_import',
        'billing_id',
        'status_jasa',
        'jasa_tindakan_bulan',
        'repo_id'
    ];

    public function get_data_surety(){
        $dt = Servant::connect_simrs('GET','get_penjamin');
		$dt = json_decode($dt);
		return $dt->response;
    }

    public static function boot() {
        parent::boot();
        static::created(function($item) {
            /* $employee = Employee::where("emp_nip",$item->nip)->first();           
            $point = [
                'bulan_jaspel'      => $item->jasa_tindakan_bulan,
                'bulan_pelayanan'   => $item->bulan_pelayanan,
                'id_tindakan'       => $item->tindakan_id,
                'penjamin'          => $item->nama_penjamin,
                'skor'              => $item->skor_jasa,
                'is_eksekutif'      => $item->unit_vip,
                'employee_id'       => $employee->emp_id,
                'repo_id'           => $item->repo_id,
                'jenis_tagihan'     => $item->jenis_tagihan,
            ];
            Point_medis::create($point); */
        });
    }
}
