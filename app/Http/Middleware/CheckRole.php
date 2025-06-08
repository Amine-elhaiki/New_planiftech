<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Non authentifié'], 401);
            }
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Vérifier si l'utilisateur a le bon rôle
        if ($user->role !== $role) {
            if ($request->expectsJson()) {
                return response()->json(['message' => "Accès refusé. Rôle '$role' requis."], 403);
            }

            abort(403, "Accès non autorisé. Seuls les utilisateurs avec le rôle '$role' peuvent accéder à cette section.");
        }

        return $next($request);
    }
}
