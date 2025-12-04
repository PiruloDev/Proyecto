@extends('layouts.app')

@section('title', 'Dashboard Cliente - Panadería')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/styleclienteds.css') }}">
@endpush

@section('body-class', '')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar">
            <div class="sidebar-content">
                <div class="sidebar-brand">
                    <img src="{{ asset('images/logoprincipal.jpg') }}" alt="Logo Panadería" class="sidebar-logo">
                    <h5>Portal Cliente</h5>
                </div>
                <!-- Botón Explorar Productos debajo del logo -->
                <div class="px-3 mb-3">
                    <a href="{{ url('/') }}" class="btn btn-explore w-100">
                        <i class="bi bi-compass"></i>
                        Explorar Productos
                    </a>
                </div>
                <div class="sidebar-divider"></div>

                <ul class="nav flex-column">
                    <div class="nav-item">
                        <a class="nav-link active" href="#dashboard" data-section="dashboard">
                            <i class="bi bi-house"></i>
                            Dashboard
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-cart-check"></i>
                            Mis Pedidos
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-person-circle"></i>
                            Mi Perfil
                        </a>
                    </div>
                </ul>

                <div class="sidebar-divider"></div>

                <div class="sidebar-user">
                    <div class="user-info">
                        <i class="bi bi-person-circle"></i>
                        <span>Cliente</span>
                    </div>
                    <a href="#" class="logout-btn mb-2 change-pass-btn">
                        <i class="bi bi-key"></i>
                        Cambiar Contraseña
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-btn">
                            <i class="bi bi-box-arrow-right"></i>
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
            <!-- Mobile menu button -->
            <button class="btn btn-outline-primary d-md-none mb-3" type="button" id="sidebarToggle">
                <i class="bi bi-list"></i> Menú
            </button>

            <!-- Dashboard Section -->
            <div class="section-content" id="dashboard-section">
                <div class="welcome-section">
                    <h2>Bienvenido, Cliente!</h2>
                    <p>Gestiona tus pedidos y explora nuestros deliciosos productos</p>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="card-icon">
                            <i class="bi bi-cart-check"></i>
                        </div>
                        <div class="card-content">
                            <div class="stat-number">{{ $totalPedidos ?? 0 }}</div>
                            <div class="stat-label">Total Pedidos</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="card-icon">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="card-content">
                            <div class="stat-number">{{ $pedidosPendientes ?? 0 }}</div>
                            <div class="stat-label">Pendientes</div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                @if(isset($pedidosRecientes) && count($pedidosRecientes) > 0)
                <div class="orders-section">
                    <div class="section-header">
                        <h4><i class="bi bi-receipt"></i> Pedidos Recientes</h4>
                        <a href="#" class="btn btn-primary btn-sm">
                            <i class="bi bi-list-ul"></i> Ver Todos
                        </a>
                    </div>
                    <div class="orders-list">
                        @foreach($pedidosRecientes as $pedido)
                        <div class="order-item">
                            <div class="order-info">
                                <h6>Pedido #{{ $pedido->id }}</h6>
                                <p class="mb-1">Por: {{ $pedido->empleado_nombre }}</p>
                                <p class="mb-1">Fecha: {{ $pedido->fecha_ingreso->format('d/m/Y H:i') }}</p>
                                <p class="mb-0">Total: ${{ number_format($pedido->total, 2) }}</p>
                            </div>
                            <div class="order-status status-{{ strtolower(str_replace(' ', '', $pedido->estado)) }}">
                                {{ $pedido->estado }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="orders-section">
                    <div class="section-header">
                        <h4><i class="bi bi-receipt"></i> Mis Pedidos</h4>
                    </div>
                    <div class="no-orders">
                        <div class="text-center py-4">
                            <i class="bi bi-cart-x" style="font-size: 3rem; color: #dee2e6;"></i>
                            <p class="text-muted">No tienes pedidos aún</p>
                            <a href="{{ route('menu') }}" class="btn btn-primary">
                                <i class="bi bi-shop"></i> Explorar Productos
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Mi Cuenta Section -->
            <div class="section-content" id="mi-cuenta-section" style="display: none;">
                <h3>Mi Cuenta</h3>
                <div class="action-cards">
                    <div class="action-card">
                        <div class="card-header">
                            <i class="bi bi-person-gear"></i>
                            <h5>Información Personal</h5>
                        </div>
                        <div class="card-body">
                            <a href="#" class="action-btn">
                                <i class="bi bi-person-lines-fill"></i>
                                Editar Perfil
                            </a>
                            <a href="#" class="action-btn">
                                <i class="bi bi-geo"></i>
                                Mis Direcciones
                            </a>
                            <a href="#" class="action-btn">
                                <i class="bi bi-key"></i>
                                Cambiar Contraseña
                            </a>
                            <a href="#" class="action-btn">
                                <i class="bi bi-gear"></i>
                                Configuración
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Navegación del sidebar
    document.addEventListener('DOMContentLoaded', function() {
        const navLinks = document.querySelectorAll('.nav-link');
        const sections = document.querySelectorAll('.section-content');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');

        // Toggle sidebar en móvil
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
        }

        // Navegación entre secciones
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href && href.startsWith('#')) {
                    e.preventDefault();

                    // Remover clase activa de todos los links
                    navLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');

                    // Ocultar todas las secciones
                    sections.forEach(s => s.style.display = 'none');

                    // Mostrar la sección seleccionada
                    const sectionId = href.substring(1) + '-section';
                    const targetSection = document.getElementById(sectionId);
                    if (targetSection) {
                        targetSection.style.display = 'block';
                    }
                }
            });
        });
    });
</script>
@endpush
