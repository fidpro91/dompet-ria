<?php

namespace App\Http\Controllers\Cores;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Builder extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function themes ($view,$data=null) {
        return view($view);
    }
}
