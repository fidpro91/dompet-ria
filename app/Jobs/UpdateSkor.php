<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Http\Controllers\EmployeeController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UpdateSkor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120000;
    public $failOnTimeout = false;

    public function __construct()
    {
        //
    }

    public function handle()
    {
        try {
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