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

                    <form method="POST" action="{{ route('login.post') }}">
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
                    </form>
                </div>
            </div>

            <!-- Informations de test -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="bi bi-info-circle text-primary me-1"></i>
                        Comptes de test
                    </h6>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">
                                <strong>Admin :</strong><br>
                                admin@ormvat.ma<br>
                                <span class="text-primary">password</span>
                            </small>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">
                                <strong>Technicien :</strong><br>
                                tech@ormvat.ma<br>
                                <span class="text-primary">password</span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .auth-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .auth-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .auth-header h1 {
        color: #007bff;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .auth-header p {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 0;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .btn-primary {
        background: linear-gradient(45deg, #007bff, #0056b3);
        border: none;
        border-radius: 8px;
        font-weight: 500;
        padding: 0.75rem;
    }

    .btn-primary:hover {
        background: linear-gradient(45deg, #0056b3, #004085);
        transform: translateY(-1px);
    }
</style>
@endpush
@endsection
