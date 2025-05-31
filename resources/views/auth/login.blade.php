@extends('layouts.app')

@section('title', 'Connexion - PlanifTech ORMVAT')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card auth-card">
                <div class="card-body">
                    <div class="auth-header">
                        <div class="text-center mb-4">
                            <i class="bi bi-water text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h1 class="h3">PlanifTech</h1>
                        <p>ORMVAT - Système de gestion des interventions</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse email</label>
                            <input id="email" type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input id="password" type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   name="password" required autocomplete="current-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                   {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Se souvenir de moi
                            </label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Se connecter
                            </button>
                        </div>

                        @if (Route::has('password.request'))
                            <div class="text-center mt-3">
                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    Mot de passe oublié ?
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Informations de test -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title">Comptes de test</h6>
                    <small class="text-muted">
                        <strong>Admin :</strong> admin@ormvat.ma / admin123<br>
                        <strong>Technicien :</strong> ahmed.bennani@ormvat.ma / technicien123
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
