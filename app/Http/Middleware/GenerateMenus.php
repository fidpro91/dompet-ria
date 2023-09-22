<?php

namespace App\Http\Middleware;

use App\Models\Ms_menu;
use Closure;
use Illuminate\Http\Request;

class GenerateMenus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        \Menu::make('MyNavBar', function ($menu) {
            $menu->add('Menu App',['class' =>'nav-small-cap'])
            ->prepend('<i class="mdi mdi-dots-horizontal"></i>');
            $hasil = Ms_menu::where('menu_parent_id', '0')->get();
            foreach($hasil as $key=>$rs){
                $menu->add($rs->menu_name, ['url'  => $rs->menu_url, 'class' => 'sidebar-item'])
                     ->prepend('<i class="'.$rs->menu_icon.'"></i> <span class="hide-menu">')
                     ->append('</span>')
                     ->link->attr(['class' => 'sidebar-link']);
            }
            $menu->add('About',    ['url'  => 'tester']);
            $menu->add('About', 'about');

            $menu->about->add('Who we are', 'about/whoweare');
            $menu->about->add('What we do', 'about/whatwedo');

            // add a class to children of About
            $menu->about->children()->attr('class', 'about-item');
            // $menu->add('Level2', ['url' => 'Link address', 'parent' => $menu->about->id]);
            /* $menu->add('Services', ['route'  => 'ms_user.index', 'class' => 'sidebar-item'])
                 ->prepend('<i class="fa fa-circle-o"></i> <span class="hide-menu">')
                 ->append('</span>')
                 ->link->attr(['class' => 'sidebar-link']); */
            /* $menu->add('Services', ['route'  => 'ms_user.index', 'class' => 'sidebar-item']);
            $menu->add('Contact', 'contact'); */
        });
        return $next($request);
    }
}
