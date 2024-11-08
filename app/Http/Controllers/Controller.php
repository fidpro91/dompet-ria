<?php

namespace App\Http\Controllers;

use App\Libraries\Servant;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function themes ($view,$data=null,$page) {
        if (is_object($page)) {
            $page = (new \ReflectionClass($page))->getShortName();
            $data['breadcumb']  = str_replace('controller','',strtolower($page));
        }else{
            $data['breadcumb']  = 'home';
        }
        $data['pageName']   = ucwords(str_replace('_',' ',$page));
        $menu='';
        if(!Session::has('loginMenu')){
            $menu = Servant::get_menu();
            Session::put("loginMenu",$menu);
            // Cache::add('menuCache', $menu, 60);
        }
        $menu = Session::get('loginMenu');
        $data['menu']       = $menu;
        return view($view,$data);
    }
}
