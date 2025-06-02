{{--
==================================================
FICHIER : resources/views/layouts/app.blade.php
DESCRIPTION : Layout principal sans navbar
AUTEUR : PlanifTech ORMVAT
==================================================
--}}

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'PlanifTech - ORMVAT')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles personnalisés -->
    @if(file_exists(public_path('css/custom.css')))
        <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    @endif

    @stack('styles')
</head>
<body>
    <div id="app">
        <!-- Contenu principal -->
        <main class="@guest container @endguest">
            @yield('content')
        </main>

        <!-- Footer seulement pour les pages de connexion -->
        @guest
            @include('layouts.footer')
        @endguest
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts personnalisés -->
    @if(file_exists(public_path('js/custom.js')))
        <script src="{{ asset('js/custom.js') }}"></script>
    @endif

    @stack('scripts')
</body>
</html>
