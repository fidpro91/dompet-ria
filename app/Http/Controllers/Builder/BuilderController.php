<?php

namespace App\Http\Controllers\Builder;

use App\Http\Controllers\Cores\Builder;

class BuilderController extends Builder
{
    public function index()
    {
        return $this->themes('builder.index');
        // return view('builder.index');
    }
}