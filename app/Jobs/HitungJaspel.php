<?php

namespace App\Jobs;

use App\Models\Jasa_pelayanan;
use Illuminate\Support\Facades\Cache;
use App\Models\Komponen_jasa_sistem;
use App\Models\Repository_download;
use App\Models\Skor_pegawai;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class HitungJaspel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $jaspelId,$repoId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($jaspelId,$repoId)
    {
        $this->jaspelId     = $jaspelId;
        $this->repoId       = $repoId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $komponen = Komponen_jasa_sistem::where("komponen_active","t")->get();

        $jaspel = app("Jasa_pelayananController");
        foreach ($komponen as $key => $value) {
            $jaspel->simpan_per_proporsi($this->jaspelId,$value->id);
        }

        $repo    = Repository_download::find($this->repoId);

        //skor pegawai
        Skor_pegawai::where("bulan_update",$repo->bulan_pelayanan)->update([
            "prepare_remun"             => "f",
            "prepare_remun_month"       => null,
        ]);

        array_map(fn($key) => Cache::forget($key), ['cacheInputJasa', 'cacheJasaHeader', 'cacheJasaProporsi', 'cacheJasaMerger']);
        
        Jasa_pelayanan::findOrFail($this->jaspelId)->update([
            "status"    => 2
        ]);
    }
}
