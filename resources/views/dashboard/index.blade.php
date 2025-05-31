@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Tableau de bord</h1>
                <span class="badge bg-success">{{ auth()->user()->role === 'admin' ? 'Administrateur' : 'Technicien' }}</span>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Carte de bienvenue -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-person-check me-2"></i>
                        Bienvenue, {{ auth()->user()->prenom }} {{ auth()->user()->nom }} !
                    </h5>
                    <p class="card-text">
                        Vous êtes connecté en tant que <strong>{{ auth()->user()->role === 'admin' ? 'Administrateur' : 'Technicien' }}</strong>.
                        <br>Email : {{ auth()->user()->email }}
                        @if(auth()->user()->telephone)
                            <br>Téléphone : {{ auth()->user()->telephone }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Navigation rapide -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-list-check text-primary" style="font-size: 2rem;"></i>
                    <h5 class="card-title mt-2">Tâches</h5>
                    <p class="card-text">Gérer les tâches et interventions</p>
                    <a href="{{ route('tasks.index') }}" class="btn btn-primary">Accéder</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-calendar-event text-success" style="font-size: 2rem;"></i>
                    <h5 class="card-title mt-2">Événements</h5>
                    <p class="card-text">Planifier et suivre les événements</p>
                    <a href="{{ route('events.index') }}" class="btn btn-success">Accéder</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-folder text-warning" style="font-size: 2rem;"></i>
                    <h5 class="card-title mt-2">Projets</h5>
                    <p class="card-text">Suivre l'avancement des projets</p>
                    <a href="{{ route('projects.index') }}" class="btn btn-warning">Accéder</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-file-text text-info" style="font-size: 2rem;"></i>
                    <h5 class="card-title mt-2">Rapports</h5>
                    <p class="card-text">Consulter et créer des rapports</p>
                    <a href="{{ route('reports.index') }}" class="btn btn-info">Accéder</a>
                </div>
            </div>
        </div>

        @if(auth()->user()->isAdmin())
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-people text-danger" style="font-size: 2rem;"></i>
                    <h5 class="card-title mt-2">Utilisateurs</h5>
                    <p class="card-text">Gérer les utilisateurs</p>
                    <a href="{{ route('users.index') }}" class="btn btn-danger">Accéder</a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
