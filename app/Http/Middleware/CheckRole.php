<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        $user = $request->user();
        
        if (!$user || !in_array($user->role_id, explode('|', $role))) {
            return redirect()->route('home'); // O cualquier otra ruta de redirección
        }

        return $next($request);
    }
}

