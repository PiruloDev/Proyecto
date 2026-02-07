@extends('layouts.app')

@section('title', 'Dashboard Cliente - Panadería')

@push('styles')
<link href="{{ asset('css/variables.css') }}" rel="stylesheet">
<link href="{{ asset('css/dashboard-client.css') }}" rel="stylesheet">
@endpush

@section('body-class', '')

@section('content')

@php
    $pedidos = $pedidos ?? [];
    $totalPedidos = count($pedidos);
    $pedidosInvertidos = array_reverse($pedidos);
    $pedidosPendientes = 0;

    foreach ($pedidos as $pedido) {
        // Java usa ID_ESTADO_PEDIDO según tu log de éxito
        $estadoId = $pedido['ID_ESTADO_PEDIDO'] ?? $pedido['id_ESTADO_PEDIDO'] ?? 0;
        if ((int)$estadoId === 1) {
            $pedidosPendientes++;
        }
    }

    $pedidosRecientes = array_slice($pedidos, 0, 5);

    function formatApiDate($dateString) {
        if (empty($dateString) || $dateString == 'N/A') return 'Pendiente';

        try {
            // Carbon es la librería de fechas de Laravel, es mucho más potente
            return \Carbon\Carbon::parse($dateString)->format('d/m/Y h:i A');
        } catch (\Exception $e) {
            return $dateString;
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
                        <div class="d-flex flex-column">
                            <span id="client-name" class="fw-bold">Cliente</span>
                            <small id="client-role" class="text-muted">Cliente</small>
                        </div>
                    </div>
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
    <div class="section-header mb-4">
        <h4><i class="bi bi-receipt"></i> Pedidos Recientes</h4>
    </div>

    @if($totalPedidos > 0)
        <div class="orders-list">
            @foreach($pedidosRecientes as $pedido)
                <div class="order-item shadow-sm mb-3 p-3 border rounded">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="order-info">
                            <h6 class="fw-bold">Pedido #{{ $pedido['ID_PEDIDO'] ?? $pedido['id_PEDIDO'] ?? 'N/A' }}</h6>
                            <p class="text-muted small mb-1">
                                <i class="bi bi-calendar3"></i> {{ formatApiDate($pedido['FECHA_INGRESO'] ?? $pedido['fecha_INGRESO'] ?? null) }}
                            </p>
                            <p class="fw-bold mb-0 text-primary">
                                Total: ${{ number_format($pedido['TOTAL_PRODUCTO'] ?? $pedido['total_PRODUCTO'] ?? 0, 2) }}
                            </p>
                        </div>
                        <div class="order-status-badge">
                            <span class="badge rounded-pill
                                @if(($pedido['ID_ESTADO_PEDIDO'] ?? $pedido['id_ESTADO_PEDIDO'] ?? 0) == 1) bg-warning text-dark
                                @elseif(($pedido['ID_ESTADO_PEDIDO'] ?? $pedido['id_ESTADO_PEDIDO'] ?? 0) == 3) bg-success
                                @else bg-info @endif">
                                Estado: {{ $pedido['ID_ESTADO_PEDIDO'] ?? $pedido['id_ESTADO_PEDIDO'] ?? 'Pendiente' }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- Vista simplificada cuando no hay pedidos --}}
        <div class="p-4 border rounded bg-light text-center">
            <p class="text-muted mb-0">No se encontraron pedidos recientes en tu historial.</p>
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
                                    <td>#{{ $pedido['id_PEDIDO'] ?? 'N/A' }}</td>
                                    <td>{{ formatApiDate($pedido['fecha_INGRESO'] ?? null) }}</td>
                                    <td>${{ number_format($pedido['total_PRODUCTO'] ?? 0, 2) }}</td>
                                    <td>
                                        <span class="badge
                                            @if(($pedido['id_ESTADO_PEDIDO'] ?? 0) === 1) bg-warning text-dark
                                            @elseif(($pedido['id_ESTADO_PEDIDO'] ?? 0) === 2) bg-info
                                            @elseif(($pedido['id_ESTADO_PEDIDO'] ?? 0) === 3) bg-success
                                            @else bg-secondary
                                            @endif
                                        ">
                                            {{ $pedido['id_ESTADO_PEDIDO'] ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ formatApiDate($pedido['fecha_ENTREGA'] ?? null) }}</td>
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
                <div class="account-box">
                    <h3>Configuración</h3>
                    <div class="profile-actions">
                        <a class="action-btn">
                            <i class="bi bi-phone"></i>
                            Cambiar Teléfono
                        </a>
                        <a class="action-btn">
                            <i class="bi bi-shield-lock"></i>
                            Cambiar Contraseña
                        </a>
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

        // Actualizar nombre y rol en sidebar
        const clientNameElement = document.getElementById('client-name');
        const clientRoleElement = document.getElementById('client-role');

        if (clientNameElement && userData && userData.nombre) {
            clientNameElement.textContent = userData.nombre;
        }

        if (clientRoleElement && userRole) {
            clientRoleElement.textContent = userRole === 'CLIENTE' || userRole === 'CLIENT' ? 'Cliente' : userRole;
        }

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
