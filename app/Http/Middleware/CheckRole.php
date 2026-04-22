<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Roles del sistema:
     * 1 = Usuario normal
     * 2 = Admin
     * 3 = Nutricionista
     * 4 = Entrenador
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userRole = auth()->user()->role;

        // Convertir roles a array de ints
        $allowedRoles = array_map('intval', $roles);

        if (!in_array($userRole, $allowedRoles)) {
            // Determinar la ruta por defecto según rol
            $route = match((int)$userRole) {
                2 => 'dashboard.admin',
                3 => 'dashboard.nutritionist',
                4 => 'dashboard.trainer',
                default => 'dashboard.index',
            };

            // Prevenir loop infinito si ya está intentando acceder a su ruta por defecto
            if ($request->routeIs($route)) {
                return abort(403, 'Acceso denegado.');
            }

            // Redirigir a su dashboard correspondiente
            return redirect()->route($route)
                ->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
