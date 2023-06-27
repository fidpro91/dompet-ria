<?php

namespace App\Http\Controllers\Mobile;

use App\Libraries\Servant;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cache;

class MobileController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}