<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function unauthenticated($request, array $guards)
    {
        // Jika request adalah AJAX dan session habis
        if ($request->ajax()) {
            abort(response()->json(['error' => 'Sesi telah habis. Silakan login kembali.'], 401));
        }

        // Jika bukan AJAX, redirect ke halaman login
        parent::unauthenticated($request, $guards);
    }

    protected function redirectTo($request)
    {
        // Redirect ke halaman login jika request bukan JSON
        if (!$request->expectsJson()) {
            return route('login');
        }
    }
}
