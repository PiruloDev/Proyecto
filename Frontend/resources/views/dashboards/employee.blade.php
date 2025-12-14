@extends('layouts.app')

@section('title', 'Dashboard Empleado - Panadería')

@push('styles')
<link href="{{ asset('css/variables.css') }}" rel="stylesheet">
<link href="{{ asset('css/dashboard-employee.css') }}" rel="stylesheet">
@endpush

@section('body-class', '')

@section('content')
<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block sidebar">
            <div class="sidebar-content">
                <div class="sidebar-brand">
                    <img src="{{ asset('images/logoprincipal.jpg') }}" alt="Logo Panadería" class="sidebar-logo">
                    <h5>Portal Empleado</h5>
                </div>
                <div class="sidebar-divider"></div>

                <ul class="nav flex-column">
                    <div class="nav-item">
                        {{-- Enlace a Dashboard --}}
                        <a class="nav-link active" href="#dashboard" data-section="dashboard">
                            <i class="bi bi-house"></i>
                            Dashboard
                        </a>
                    </div>
                    <div class="nav-item">
                        {{-- ENLACE CORREGIDO: Usará la ruta 'pedidos.index' --}}
                        <a class="nav-link" href="{{ route('pedidos.index') }}" data-section="pedidos-index">
                            <i class="bi bi-cart-check"></i>
                            Gestionar Pedidos
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" href="#productos" data-section="productos">
                            <i class="bi bi-box-seam"></i>
                            Ver Productos
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" href="#perfil" data-section="perfil">
                            <i class="bi bi-person-circle"></i>
                            Mi Perfil
                        </a>
                    </div>
                </ul>

                <div class="sidebar-divider"></div>

                <div class="sidebar-user">
                    <div class="user-info">
                        <i class="bi bi-person-circle"></i>
                        <span>Empleado</span>
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

            <div class="section-content" id="dashboard-section">
                <div class="welcome-section mb-4">
                    <h2>Bienvenido, Empleado!</h2>
                    <p>Panel de control del empleado</p>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card">
                            <div class="card-icon">
                                <i class="bi bi-cart-check"></i>
                            </div>
                            <div class="card-content">
                                <div class="stat-number">{{ $pedidosHoy ?? 0 }}</div>
                                <div class="stat-label">Pedidos Hoy</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
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
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card">
                            <div class="card-icon">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div class="card-content">
                                <div class="stat-number">{{ $productosDisponibles ?? 0 }}</div>
                                <div class="stat-label">Productos Disponibles</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card">
                            <div class="card-icon">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="card-content">
                                <div class="stat-number">{{ $totalPedidos ?? 0 }}</div>
                                <div class="stat-label">Total Pedidos</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="orders-section">
                    <div class="section-header mb-3">
                        <h4> Acciones Rápidas</h4>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="action-card text-center p-4">
                                <i class="bi bi-plus-circle fs-1 text-primary mb-3"></i>
                                <h5>Crear Pedido</h5>
                                <p class="text-muted">Registrar un nuevo pedido</p>
                                {{-- ENLACE CORREGIDO: a la vista de creación --}}
                                <a href="{{ route('pedidos.create') }}" class="btn btn-primary w-100">Crear</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="action-card text-center p-4">
                                <i class="bi bi-list-check fs-1 text-success mb-3"></i>
                                <h5>Ver Pedidos</h5>
                                <p class="text-muted">Revisar pedidos pendientes</p>
                                {{-- ENLACE CORREGIDO: a la vista de índice/listado --}}
                                <a href="{{ route('pedidos.index') }}" class="btn btn-success w-100">Ver</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Aquí irían otras secciones del dashboard (pedidos, productos, perfil) --}}

                <div class="section-content" id="pedidos-section" style="display: none;">
                    <h3>Gestión de Pedidos</h3>
                    <p>Contenido para gestionar pedidos. Incluiría la tabla de pedidos pendientes, etc.</p>
                </div>

                <div class="section-content" id="productos-section" style="display: none;">
                    <h3>Productos</h3>
                    <p>Contenido para visualizar productos disponibles.</p>
                </div>

                <div class="section-content" id="perfil-section" style="display: none;">
                    <h3>Mi Perfil</h3>
                    <p>Contenido de la información del empleado.</p>
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

        if (userRole !== 'EMPLEADO') {
            console.warn('Usuario no autorizado para dashboard empleado');
            const correctDashboard = AuthManager.getDashboardRoute(userRole);
            window.location.href = correctDashboard;
            return;
        }

        console.log('Dashboard Empleado - Usuario autenticado:', userData);

        const navLinks = document.querySelectorAll('.nav-link');
        const sections = document.querySelectorAll('.section-content');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');

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

                    sections.forEach(s => s.style.display = 'none');

                    const sectionId = href.substring(1) + '-section';
                    const targetSection = document.getElementById(sectionId);
                    if (targetSection) {
                        targetSection.style.display = 'block';
                    }
                }
            });
        });

        const dashboardSection = document.getElementById('dashboard-section');
        if (dashboardSection) {
            dashboardSection.style.display = 'block';
        }
    });
</script>
@endpush
