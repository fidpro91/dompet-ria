<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\EmployeeController;

class TesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    // public $timeout = 120;

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
        try {
            Log::info('UpdateSkor job started');
            $employees = Employee::where("emp_active", "t")->get();
            foreach ($employees as $employee) {
                $employeeController = new EmployeeController;
                $req = new Request($employee->toArray());
                $skorPegawai = $employeeController->get_unit_skor($req);
                if ($skorPegawai["code"] != 200) {
                    continue;
                }
                Employee::find($employee->emp_id)->update([
                    'last_risk_index'          => $skorPegawai["response"]["riskIndex"],
                    'last_emergency_index'     => $skorPegawai["response"]["emergencyIndex"],
                    'last_position_index'      => $skorPegawai["response"]["positionIndex"]
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error in UpdateSkor job: ' . $e->getMessage());
            throw $e;
        }
    }
}
