<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Roles permitidos para esta ruta.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Usuario no autenticado
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Obtener usuario auth
        $user = Auth::user();

        // Verificar si su rol está en la lista permitida
        if (! in_array($user->role, $roles)) {
            return abort(403, 'No tienes permiso para acceder a esta página.');
        }

        return $next($request);
    }
}
