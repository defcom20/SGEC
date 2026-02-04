<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $modulo, string $accion): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admin tiene acceso a todo
        if ($user->rol?->nombre === 'admin') {
            return $next($request);
        }

        // Verificar si el usuario tiene el permiso
        $tienePermiso = $user->rol?->permisos()
            ->where('modulo', $modulo)
            ->where('accion', $accion)
            ->exists();

        if (!$tienePermiso) {
            abort(403, 'No tienes permiso para acceder a este módulo.');
        }

        return $next($request);
    }
}
