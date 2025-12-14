@extends('layouts.app')

@section('title', 'Dashboard Cliente - Panadería')

@push('styles')
<link href="{{ asset('css/variables.css') }}" rel="stylesheet">
<link href="{{ asset('css/dashboard-client.css') }}" rel="stylesheet">
@endpush

@section('body-class', '')

@section('content')

@php
    // ----------------------------------------------------
    //  LÓGICA DE CÁLCULO DE ESTADÍSTICAS (BLADE PHP)
    // ----------------------------------------------------
    $pedidos = $pedidos ?? []; // Asegura que $pedidos exista y sea un array
    $totalPedidos = count($pedidos);
    $pedidosPendientes = 0;

    // Suponiendo que ID_ESTADO_PEDIDO = 1 es "Pendiente" (ajusta según tu base de datos)
    foreach ($pedidos as $pedido) {
        if (($pedido['ID_ESTADO_PEDIDO'] ?? 0) === 1) {
            $pedidosPendientes++;
        }
    }

    // Tomamos los 5 pedidos más recientes para la sección "Recientes"
    // Los pedidos de la API de Spring Boot deberían venir ordenados por FECHA_INGRESO descendente
    $pedidosRecientes = array_slice($pedidos, 0, 5);

    // Función auxiliar para formatear la fecha (el objeto de la API es una cadena)
    function formatApiDate($dateString) {
        if (empty($dateString)) return 'N/A';
        try {
            return date('d/m/Y H:i', strtotime($dateString));
        } catch (\Exception $e) {
            return $dateString; // Devuelve la cadena si falla el formato
        }
    }
@endphp

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block sidebar">
            <div class="sidebar-content">
                <div class="sidebar-brand">
                    <img src="{{ asset('images/logoprincipal.jpg') }}" alt="Logo Panadería" class="sidebar-logo">
                    <h5>Portal Cliente</h5>
                </div>
                <div class="px-3 mb-3">
                    {{-- Asume que 'menu' es la ruta para la tienda --}}
                    <a href="{{ route('menu') }}" class="btn btn-explore w-100">
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
                    {{-- Enlace a la sección "Mis Pedidos" --}}
                    <div class="nav-item">
                        <a class="nav-link" href="#pedidos" data-section="pedidos">
                            <i class="bi bi-cart-check"></i>
                            Mis Pedidos
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" href="#mi-cuenta" data-section="mi-cuenta">
                            <i class="bi bi-person-circle"></i>
                            Mi Perfil
                        </a>
                    </div>
                </ul>

                <div class="sidebar-divider"></div>

                <div class="sidebar-user">
                    <div class="user-info">
                        <i class="bi bi-person-circle"></i>
                        {{-- Muestra el nombre del usuario autenticado si es posible --}}
                        <span>{{ Auth::user()->name ?? 'Cliente' }}</span>
                    </div>
                    <a href="#" class="logout-btn mb-2 change-pass-btn">
                        <i class="bi bi-key"></i>
                        Cambiar Contraseña
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="logout-form">
                        @csrf
                        <button type="submit" class="logout-btn">
                            <i class="bi bi-box-arrow-right"></i>
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
            <button class="btn btn-outline-primary d-md-none mb-3" type="button" id="sidebarToggle">
                <i class="bi bi-list"></i> Menú
            </button>

            {{-- Mensajes de error globales --}}
            @if(session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="section-content" id="dashboard-section" style="display: block;">
                <div class="welcome-section">
                    <h2>Bienvenido, Cliente!</h2>
                    <p>Aquí puedes gestionar tus pedidos y explorar nuestros deliciosos productos</p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="card-icon">
                            <i class="bi bi-cart-check"></i>
                        </div>
                        <div class="card-content">
                            <div class="stat-number">{{ $totalPedidos }}</div>
                            <div class="stat-label">Total Pedidos</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="card-icon">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="card-content">
                            <div class="stat-number">{{ $pedidosPendientes }}</div>
                            <div class="stat-label">Pendientes</div>
                        </div>
                    </div>
                </div>

                <div class="orders-section">
                    <div class="section-header">
                        <h4><i class="bi bi-receipt"></i> Pedidos Recientes</h4>
                        <a href="#" data-section="pedidos" class="btn btn-primary btn-sm nav-link-trigger">
                            <i class="bi bi-list-ul"></i> Ver Todos
                        </a>
                    </div>

                    @if($totalPedidos > 0)
                    <div class="orders-list">
                        @foreach($pedidosRecientes as $pedido)
                        <div class="order-item">
                            <div class="order-info">
                                <h6>Pedido #{{ $pedido['ID_PEDIDO'] ?? 'N/A' }}</h6>
                                {{-- ⚠️ NOTA: El nombre del empleado y estado se obtienen de la base de datos de Spring Boot,
                                    si no vienen en el JSON devuelto, estos campos mostrarán un valor por defecto.
                                    Aquí solo usamos los datos que vienen en la API. --}}
                                <p class="mb-1">Fecha Ingreso: {{ formatApiDate($pedido['FECHA_INGRESO'] ?? null) }}</p>
                                <p class="mb-0">Total: ${{ number_format($pedido['TOTAL_PRODUCTO'] ?? 0, 2) }}</p>
                            </div>
                            <div class="order-status
                                @if(($pedido['ID_ESTADO_PEDIDO'] ?? 0) === 1) status-pendiente
                                @elseif(($pedido['ID_ESTADO_PEDIDO'] ?? 0) === 3) status-entregado
                                @else status-otro
                                @endif
                                ">
                                {{-- Aquí deberías mapear ID_ESTADO_PEDIDO a un nombre (Ej: 1 -> Pendiente) --}}
                                Estado: {{ $pedido['ID_ESTADO_PEDIDO'] ?? 'N/A' }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                        <div class="no-orders">
                            <div class="text-center py-4">
                                <i class="bi bi-cart-x" style="font-size: 3rem; color: #dee2e6;"></i>
                                <p class="text-muted">No tienes pedidos aún</p>
                                <a href="{{ route('menu') }}" class="btn btn-primary">
                                    <i class="bi bi-shop"></i> Explorar Productos
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="section-content" id="pedidos-section" style="display: none;">
                <h3 class="mb-4">Todos Mis Pedidos</h3>

                @if($totalPedidos > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover orders-table">
                        <thead>
                            <tr>
                                <th>ID Pedido</th>
                                <th>Fecha Ingreso</th>
                                <th>Total</th>
                                <th>Estado ID</th>
                                <th>Fecha Entrega</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pedidos as $pedido)
                                <tr>
                                    <td>#{{ $pedido['ID_PEDIDO'] ?? 'N/A' }}</td>
                                    <td>{{ formatApiDate($pedido['FECHA_INGRESO'] ?? null) }}</td>
                                    <td>${{ number_format($pedido['TOTAL_PRODUCTO'] ?? 0, 2) }}</td>
                                    {{-- Aquí se muestra el ID del estado. Idealmente se mapea el nombre. --}}
                                    <td>
                                        <span class="badge
                                            @if(($pedido['ID_ESTADO_PEDIDO'] ?? 0) === 1) bg-warning text-dark
                                            @elseif(($pedido['ID_ESTADO_PEDIDO'] ?? 0) === 2) bg-info
                                            @elseif(($pedido['ID_ESTADO_PEDIDO'] ?? 0) === 3) bg-success
                                            @else bg-secondary
                                            @endif
                                        ">
                                            {{ $pedido['ID_ESTADO_PEDIDO'] ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ formatApiDate($pedido['FECHA_ENTREGA'] ?? null) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <div class="alert alert-info">No tienes pedidos registrados.</div>
                @endif
            </div>

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
    document.addEventListener('DOMContentLoaded', function() {
        if (!AuthManager.isAuthenticated()) {
            AuthManager.redirectToLogin();
            return;
        }

        const userData = AuthManager.getUserData();
        const userRole = AuthManager.getRole();

        if (userRole !== 'CLIENTE' && userRole !== 'CLIENT') {
            console.warn('Usuario no autorizado para dashboard cliente');
            const correctDashboard = AuthManager.getDashboardRoute(userRole);
            window.location.href = correctDashboard;
            return;
        }

        console.log('Dashboard Cliente - Usuario autenticado:', userData);

        const navLinks = document.querySelectorAll('.nav-link');
        const sections = document.querySelectorAll('.section-content');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        const navLinkTriggers = document.querySelectorAll('.nav-link-trigger');

        function showSection(sectionId) {
            sections.forEach(s => s.style.display = 'none');
            const targetSection = document.getElementById(sectionId);
            if (targetSection) {
                targetSection.style.display = 'block';
            }
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
        }

        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href && href.startsWith('#')) {
                    e.preventDefault();

                    navLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');

                    const sectionId = href.substring(1) + '-section';
                    showSection(sectionId);
                }
            });
        });

        navLinkTriggers.forEach(trigger => {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                const targetSection = this.getAttribute('data-section');
                const sectionId = targetSection + '-section';

                navLinks.forEach(l => {
                    l.classList.remove('active');
                    if (l.getAttribute('data-section') === targetSection) {
                        l.classList.add('active');
                    }
                });

                showSection(sectionId);
            });
        });

        showSection('dashboard-section');
    });
</script>
@endpush
