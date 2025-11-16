<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Gestion de Flotte</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #f8f9fa;
            border-right: 1px solid #dee2e6;
        }
        .sidebar .nav-link {
            color: #495057;
            padding: 0.75rem 1rem;
        }
        .sidebar .nav-link:hover {
            background-color: #e9ecef;
        }
        .sidebar .nav-link.active {
            background-color: #007bff;
            color: white;
        }
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark bg-primary shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('dashboard') }}">
                    <i class="fas fa-car-side"></i> Gestion de Flotte
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>

                            @can('view_vehicles')
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->is('vehicles*') ? 'active' : '' }}" href="#" id="fleetDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-car"></i> Flotte
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="fleetDropdown">
                                    <li><a class="dropdown-item" href="{{ route('vehicles.index') }}"><i class="fas fa-list"></i> Liste Véhicules</a></li>
                                    @can('create_vehicles')
                                    <li><a class="dropdown-item" href="{{ route('vehicles.create') }}"><i class="fas fa-plus"></i> Nouveau Véhicule</a></li>
                                    @endcan
                                    <li><hr class="dropdown-divider"></li>
                                    @can('view_fuel')
                                    <li><a class="dropdown-item" href="{{ route('fuel-consumptions.index') }}"><i class="fas fa-gas-pump"></i> Carburant</a></li>
                                    @can('create_fuel')
                                    <li><a class="dropdown-item" href="{{ route('fuel-consumptions.create') }}"><i class="fas fa-plus"></i> Ravitaillement</a></li>
                                    @endcan
                                    @endcan
                                    @can('view_documents')
                                    <li><a class="dropdown-item" href="{{ route('vehicle-documents.index') }}"><i class="fas fa-file-alt"></i> Documents</a></li>
                                    @can('create_documents')
                                    <li><a class="dropdown-item" href="{{ route('vehicle-documents.create') }}"><i class="fas fa-plus"></i> Nouveau Document</a></li>
                                    @endcan
                                    <li><a class="dropdown-item" href="{{ route('vehicle-documents.alerts') }}"><i class="fas fa-bell"></i> Alertes</a></li>
                                    @endcan
                                    <li><hr class="dropdown-divider"></li>
                                    @can('view_accidents')
                                    <li><a class="dropdown-item" href="{{ route('accidents.index') }}"><i class="fas fa-car-crash"></i> Accidents</a></li>
                                    @can('create_accidents')
                                    <li><a class="dropdown-item" href="{{ route('accidents.create') }}"><i class="fas fa-plus"></i> Déclarer Accident</a></li>
                                    @endcan
                                    @endcan
                                    @can('view_violations')
                                    <li><a class="dropdown-item" href="{{ route('traffic-violations.index') }}"><i class="fas fa-exclamation-circle"></i> Infractions</a></li>
                                    @can('create_violations')
                                    <li><a class="dropdown-item" href="{{ route('traffic-violations.create') }}"><i class="fas fa-plus"></i> Nouvelle Infraction</a></li>
                                    @endcan
                                    @endcan
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('insurances.index') }}"><i class="fas fa-shield-alt"></i> Assurances</a></li>
                                    <li><a class="dropdown-item" href="{{ route('contracts.index') }}"><i class="fas fa-file-contract"></i> Contrats</a></li>
                                    <li><a class="dropdown-item" href="{{ route('rentals.index') }}"><i class="fas fa-handshake"></i> Locations</a></li>
                                </ul>
                            </li>
                            @endcan

                            @can('view_interventions')
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="maintenanceDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-tools"></i> Maintenance
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="maintenanceDropdown">
                                    <li><a class="dropdown-item" href="{{ route('interventions.index') }}"><i class="fas fa-wrench"></i> Interventions</a></li>
                                    @can('create_interventions')
                                    <li><a class="dropdown-item" href="{{ route('interventions.create') }}"><i class="fas fa-plus"></i> Nouvelle Intervention</a></li>
                                    @endcan
                                    <li><hr class="dropdown-divider"></li>
                                    @can('view_vehicles')
                                    <li><a class="dropdown-item" href="{{ route('tires.index') }}"><i class="fas fa-tire"></i> Pneumatiques</a></li>
                                    @can('create_vehicles')
                                    <li><a class="dropdown-item" href="{{ route('tires.create') }}"><i class="fas fa-plus"></i> Nouveau Pneu</a></li>
                                    @endcan
                                    <li><a class="dropdown-item" href="{{ route('tires.alerts') }}"><i class="fas fa-bell"></i> Alertes Usure</a></li>
                                    @endcan
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-clipboard-check"></i> Plan Préventif</a></li>
                                </ul>
                            </li>
                            @endcan

                            @can('view_employees')
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="hrDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-users"></i> RH
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="hrDropdown">
                                    <li><a class="dropdown-item" href="{{ route('employees.index') }}"><i class="fas fa-user-tie"></i> Employés</a></li>
                                    @can('create_employees')
                                    <li><a class="dropdown-item" href="{{ route('employees.create') }}"><i class="fas fa-plus"></i> Nouvel Employé</a></li>
                                    @endcan
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('driving-licenses.index') }}"><i class="fas fa-id-card"></i> Permis de Conduire</a></li>
                                    <li><a class="dropdown-item" href="{{ route('driving-licenses.alerts') }}"><i class="fas fa-bell"></i> Alertes Permis</a></li>
                                    <li><a class="dropdown-item" href="{{ route('certifications.index') }}"><i class="fas fa-certificate"></i> Certifications</a></li>
                                    <li><a class="dropdown-item" href="{{ route('certifications.alerts') }}"><i class="fas fa-bell"></i> Alertes Certifications</a></li>
                                    @can('view_trainings')
                                    <li><a class="dropdown-item" href="{{ route('trainings.index') }}"><i class="fas fa-graduation-cap"></i> Formations</a></li>
                                    @endcan
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('medical-checkups.index') }}"><i class="fas fa-heartbeat"></i> Visites Médicales</a></li>
                                    <li><a class="dropdown-item" href="{{ route('medical-checkups.alerts') }}"><i class="fas fa-bell"></i> Alertes Médicales</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('ppe.index') }}"><i class="fas fa-hard-hat"></i> EPI</a></li>
                                    <li><a class="dropdown-item" href="{{ route('ppe.alerts') }}"><i class="fas fa-bell"></i> Alertes EPI</a></li>
                                </ul>
                            </li>
                            @endcan

                            @can('view_stock')
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="stockDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-boxes"></i> Stocks
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="stockDropdown">
                                    <li><a class="dropdown-item" href="{{ route('inventory-parts.index') }}"><i class="fas fa-cogs"></i> Pièces</a></li>
                                    <li><a class="dropdown-item" href="{{ route('inventory-parts.alerts') }}"><i class="fas fa-bell"></i> Alertes Stock</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('suppliers.index') }}"><i class="fas fa-truck"></i> Fournisseurs</a></li>
                                </ul>
                            </li>
                            @endcan

                            @can('view_transport_orders')
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="transportDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-truck"></i> Transport
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="transportDropdown">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-route"></i> Missions</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-invoice"></i> Commandes</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-bus"></i> Lignes Régulières</a></li>
                                </ul>
                            </li>
                            @endcan

                            @can('view_gps')
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-map-marker-alt"></i> GPS
                                </a>
                            </li>
                            @endcan
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="fas fa-user-circle"></i> {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-user"></i> Mon Profil
                                    </a>
                                    @can('manage_settings')
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-cog"></i> Paramètres
                                    </a>
                                    @endcan
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Erreurs de validation:</strong>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

        <footer class="bg-light text-center text-muted py-3 mt-5">
            <div class="container">
                <small>&copy; 2025 Plateforme de Gestion de Flotte Automobile. Tous droits réservés.</small>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
