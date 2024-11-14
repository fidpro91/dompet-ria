<?php

namespace App\Jobs;

use App\Models\Jp_byname_medis;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EditBynameJaspel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $byname,$totalValue,$totalSkor;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($byname,$totalSkor,$totalValue)
    {
        $this->byname       = $byname;
        $this->totalSkor    = $totalSkor;
        $this->totalValue   = $totalValue;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->byname as $key => $value) {
            $nominalTerima = ($value->skor/$this->totalSkor)*$this->totalValue;
            Jp_byname_medis::find($value->jp_medis_id)->update([
                "nominal_terima"    => $nominalTerima
            ]);
        }
    }
}
