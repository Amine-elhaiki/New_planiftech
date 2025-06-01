<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * Constructor - Seuls les admins peuvent accéder à l'inscription
     */
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Afficher le formulaire d'inscription
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Traiter l'inscription
     */
    public function register(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:50',
            'prenom' => 'required|string|max:50',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,technicien',
            'telephone' => 'nullable|string|max:20',
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'role.required' => 'Le rôle est obligatoire.',
            'role.in' => 'Le rôle doit être admin ou technicien.',
        ]);

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'statut' => 'actif',
            'telephone' => $request->telephone,
            'date_creation' => now(),
            'email_verified_at' => now(),
        ]);

        // Enregistrer la création dans le journal si la classe existe
        if (class_exists('App\Models\Journal')) {
            \App\Models\Journal::enregistrerCreation('utilisateur', $user->nom_complet, Auth::id());
        }

        // Créer une notification de bienvenue si la classe existe
        $user->creerNotification(
            'Bienvenue sur PlanifTech',
            'Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter et commencer à utiliser PlanifTech.',
            'systeme'
        );

        return redirect()->route('users.index')
                        ->with('success', 'Utilisateur créé avec succès : ' . $user->nom_complet);
    }
}
