<?php

namespace App\Http\Middleware;



use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ActiveUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur est connecté et actif
        if (Auth::check()) {
            $user = Auth::user();

            // Vérifier si l'utilisateur a un statut défini et s'il est inactif
            if (isset($user->statut) && $user->statut !== 'actif') {
                // Déconnecter l'utilisateur
                Auth::logout();

                // Invalider la session
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Votre compte a été désactivé. Contactez l\'administrateur.'
                    ], 403);
                }

                return redirect()->route('login')
                               ->withErrors(['email' => 'Votre compte a été désactivé. Contactez l\'administrateur.']);
            }
        }

        return $next($request);
    }
}
