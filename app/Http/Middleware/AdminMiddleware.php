<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Non authentifié'], 401);
            }
            return redirect()->route('login');
        }

        // Vérifier si l'utilisateur a le rôle admin
        if (Auth::user()->role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Accès refusé. Droits administrateur requis.'], 403);
            }

            abort(403, 'Accès refusé. Seuls les administrateurs peuvent accéder à cette section.');
        }

        return $next($request);
    }
}

