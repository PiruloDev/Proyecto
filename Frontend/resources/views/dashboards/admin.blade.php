@extends('layouts.app')

@section('title', 'Dashboard Administrador - Panadería')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/styleadmindst.css') }}">
@endpush

@section('body-class', '')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar">
            <div class="sidebar-content">
                <div class="sidebar-brand">
                    <h5>Portal Admin</h5>
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
                        <a class="nav-link" href="#productos">
                            <i class="bi bi-box-seam"></i>
                            Gestionar Productos
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" href="#empleados">
                            <i class="bi bi-people"></i>
                            Gestionar Empleados
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" href="#clientes">
                            <i class="bi bi-person-lines-fill"></i>
                            Gestionar Clientes
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" href="#pedidos">
                            <i class="bi bi-cart-check"></i>
                            Ver Pedidos
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard.inventario') }}"><i class="fas fa-home"></i> Inventario</a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" href="#reportes">
                            <i class="bi bi-graph-up"></i>
                            Reportes
                        </a>
                    </div>
                </ul>

                <div class="sidebar-divider"></div>

                <div class="sidebar-user">
                    <div class="user-info">
                        <i class="bi bi-person-circle"></i>
                        <span>Administrador</span>
                    </div>
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
                <div class="welcome-section mb-4">
                    <h2>Panel de Administración</h2>
                    <p>Bienvenido, Administrador</p>
                </div>

                <!-- Stats Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6 col-lg-4">
                        <div class="stat-card">
                            <div class="card-icon">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="card-content">
                                <div class="stat-number">{{ $empleadosActivos ?? 0 }}</div>
                                <div class="stat-label">Empleados Activos</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="stat-card">
                            <div class="card-icon">
                                <i class="bi bi-person-check"></i>
                            </div>
                            <div class="card-content">
                                <div class="stat-number">{{ $clientesActivos ?? 0 }}</div>
                                <div class="stat-label">Clientes Activos</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
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
                </div>

                <!-- Quick Actions -->
                <div class="orders-section">
                    <div class="section-header mb-3">
                        <h4><i class="bi bi-lightning"></i> Acciones Rápidas</h4>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-4">
                            <div class="action-card text-center p-4">
                                <i class="bi bi-plus-circle fs-1 text-primary mb-3"></i>
                                <h5>Agregar Producto</h5>
                                <p class="text-muted">Registrar nuevo producto</p>
                                <button class="btn btn-primary w-100">Agregar</button>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="action-card text-center p-4">
                                <i class="bi bi-person-plus fs-1 text-success mb-3"></i>
                                <h5>Nuevo Empleado</h5>
                                <p class="text-muted">Registrar empleado</p>
                                <button class="btn btn-success w-100">Crear</button>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="action-card text-center p-4">
                                <i class="bi bi-graph-up fs-1 text-info mb-3"></i>
                                <h5>Ver Reportes</h5>
                                <p class="text-muted">Estadísticas del sistema</p>
                                <button class="btn btn-info w-100">Ver</button>
                            </div>
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
    });
</script>
@endpush
