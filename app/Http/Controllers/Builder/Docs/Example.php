<?php

namespace App\Http\Controllers\Builder\Docs;

use App\Http\Controllers\Cores\Builder;

class Example extends Builder
{
    public function form_basic()
    {
        return $this->themes('builder.docs.form_basic');
    }

    public function form_widget()
    {
        return $this->themes('builder.docs.form_widget');
    }

    public function bosstrap_comp()
    {
        return $this->themes('builder.docs.bosstrap_components');
    }

    public function load_tab_page()
    {
        return "<h3>Ini halaman load</h3>";
    }    
}