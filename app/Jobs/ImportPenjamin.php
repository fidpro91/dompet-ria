<?php

namespace App\Jobs;

use App\Models\Ms_reff;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ImportPenjamin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ehos = DB::connection('ehos');
        $data = $ehos->table("yanmed.ms_surety")->where("surety_active","t")->get();
        foreach ($data as $item) {
            $records[] = [
                'reff_code'     => $item->surety_id,
                'reff_name'     => $item->surety_name,
                'reff_active'   => "t",
                'reffcat_id'    => 5,
            ];
        }
        if (!empty($records)) {
            Ms_reff::upsert(
                $records,
                ['reff_code', 'reffcat_id'], 
                ['reff_name', 'reff_active']
            );
        }
    }
}
