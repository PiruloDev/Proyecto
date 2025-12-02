@extends('layouts.app') 
{{-- ⬆️ Asume un layout maestro en resources/views/layouts/app.blade.php --}}

@section('title', 'Menú Principal - Inventario')

{{-- Usamos el @push para añadir estilos que solo necesita esta página --}}
@push('styles')
    {{-- La URL del CSS debe apuntar a la carpeta public/css/ --}}
    <link rel="stylesheet" href="{{ asset('css/stylemoduloinv.css') }}">
@endpush

@section('content')

<div class="container-fluid">
    <div class="row g-0">
        
        @include('partials.sidebar-inventario')

        <div class="col-md-9 col-lg-10 main-content">
            
            {{-- Top Navbar (basado en menu.php) --}}
            <nav class="navbar navbar-expand-lg top-navbar">
                <div class="container-fluid">
                    <a class="navbar-brand d-md-none" href="{{ route('dashboard.inventario') }}">Menú</a>
                    
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    
                    <div class="collapse navbar-collapse justify-content-end" id="navbarNavAltMarkup">
                        <div class="navbar-nav">
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="me-2 text-dark d-none d-sm-inline">Administrador</span>
                                    <div class="profile-icon-wrapper">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Configuración</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    {{-- Reemplazar # con la ruta de logout de Laravel --}}
                                    <li><a class="dropdown-item" href="{{ route('logout') }}"><i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            {{-- Sección de Bienvenida (basado en menu.php) --}}
            <div class="welcome-section">
                <h1>Bienvenido al Panel de Control</h1>
                <p class="lead" style="color: var(--text-light);">Usa el menú lateral para navegar entre los módulos de gestión.</p>
            </div>
            
        </div>
    </div>
</div>

@endsection

@push('scripts')
    {{-- Si tienes scripts que solo se cargan en el menú, irían aquí --}}
@endpush