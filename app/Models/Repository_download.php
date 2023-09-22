<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repository_download extends Model
{
    use HasFactory;

    protected $table = 'repository_download';
    protected $fillable = [
        'download_date',
        'bulan_jasa',
        'bulan_pelayanan',
        'periode_awal',
        'periode_akhir',
        'group_penjamin',
        'jenis_pembayaran',
        'download_by',
        'download_no',
        'is_used',
        'total_data',
        'skor_eksekutif',
        'skor_non_eksekutif'
    ];

    public function hasCopy()
    {
        return $this->hasMany(Jasa_pelayanan::class, 'repo_id')
                ->select('repo_id','jaspel_id')
                ->distinct();
    }
}
