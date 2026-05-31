<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthCheck
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar si el usuario ha iniciado sesión
        if (!session()->has('usuario')) {
            return redirect('/Login')->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }
        
        return $next($request);
    }
}