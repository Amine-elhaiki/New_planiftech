<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Page d'accueil - redirection vers login ou dashboard
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Routes d'authentification Laravel 12
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', function () {
        $credentials = request()->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, request()->boolean('remember'))) {
            request()->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Les informations de connexion ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    });
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

// Route temporaire pour le dashboard
Route::get('/dashboard', function () {
    return view('dashboard.index');
})->middleware('auth')->name('dashboard');

// Routes temporaires pour tester
Route::middleware(['auth'])->group(function () {
    Route::get('/tasks', function () {
        return view('tasks.index');
    })->name('tasks.index');

    Route::get('/events', function () {
        return view('events.index');
    })->name('events.index');

    Route::get('/projects', function () {
        return view('projects.index');
    })->name('projects.index');

    Route::get('/reports', function () {
        return view('reports.index');
    })->name('reports.index');

    Route::get('/profile', function () {
        return view('profile.edit');
    })->name('profile.edit');
});

// Routes admin temporaires
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/users', function () {
        return view('users.index');
    })->name('users.index');

    Route::get('/admin/logs', function () {
        return view('admin.logs');
    })->name('admin.logs');
});
