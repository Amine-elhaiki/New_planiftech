<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Afficher le formulaire de connexion
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Traiter la connexion
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // Vérifier si l'utilisateur existe et est actif
        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if ($user && $user->statut === 'inactif') {
            throw ValidationException::withMessages([
                'email' => 'Votre compte a été désactivé. Contactez l\'administrateur.',
            ]);
        }

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Enregistrer la connexion
            $user = Auth::user();
            $user->enregistrerConnexion();

            // Redirection selon le rôle
            $intendedUrl = $request->session()->get('url.intended', '/dashboard');

            return redirect()->intended($intendedUrl)->with('success',
                'Bienvenue, ' . $user->prenom . ' ' . $user->nom . ' !');
        }

        // Enregistrer l'échec de connexion si le model Journal existe
        if (class_exists('App\Models\Journal')) {
            \App\Models\Journal::enregistrerErreur("Tentative de connexion échouée pour l'email : " . $credentials['email']);
        }

        throw ValidationException::withMessages([
            'email' => 'Les informations de connexion ne correspondent pas à nos enregistrements.',
        ]);
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Enregistrer la déconnexion si le model Journal existe
        if ($user && class_exists('App\Models\Journal')) {
            \App\Models\Journal::enregistrerAction('connexion', 'Déconnexion de : ' . $user->nom_complet, $user->id);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Vous avez été déconnecté avec succès.');
    }
}
