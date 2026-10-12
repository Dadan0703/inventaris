<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // kalau rolenya admin, boleh lewat
        if (auth()->check() && auth()->user()->role == 'admin') {
            return $next($request);
        }

        // kalau staf maksa masuk, tendang ke dashboard
        return redirect('/dashboard')->with('error', 'Akses ditolak! Khusus admin.');
    }
}