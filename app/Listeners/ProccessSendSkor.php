<?php

namespace App\Listeners;

use App\Events\PublishSkor;
use App\Libraries\Qontak;
use App\Models\Ms_unit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class ProccessSendSkor
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\PublishSkor  $event
     * @return void
     */
    public function handle(PublishSkor $event)
    {
        $dataUnit = Ms_unit::from("ms_unit as mu")
                    ->join("employee as e", "e.emp_id", "=", "mu.ka_unit")
                    ->select([
                        'e.emp_name',
                        'e.phone',
                        'e.emp_id',
                        DB::raw('GROUP_CONCAT(mu.unit_name) as unit_kerja')
                    ])
                    ->groupBy('e.emp_name', 'e.phone', 'e.emp_id')
                    ->get();
        foreach ($dataUnit as $key => $value) {
            if ($value->phone) {
                Qontak::sendInfoSkor($value->phone,$value->emp_name,[
                    "penerima"  => $value->emp_name,
                    "unit"      => $value->unit_kerja
                ]);
            }
        }
    }
}
