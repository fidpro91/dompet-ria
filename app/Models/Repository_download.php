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
    ];
}
