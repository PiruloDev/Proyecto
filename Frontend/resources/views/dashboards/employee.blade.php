@extends('layouts.app')

@section('title', 'Dashboard Empleado - Panadería')

@push('styles')
<link href="{{ asset('css/variables.css') }}" rel="stylesheet">
<link href="{{ asset('css/dashboard-employee.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    /* Ajuste para que las tarjetas de acción se vean profesionales */
    .action-card {
        transition: transform 0.2s;
        border: 1px solid #eee;
        border-radius: 15px;
        background: white;
    }
    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    }
</style>
@endpush

@section('content')
<script>
    (function() {
        const logoutFlag = sessionStorage.getItem('logout_flag');
        if (logoutFlag === 'true') {
            sessionStorage.clear();
            window.location.replace('/login');
        }
    })();
</script>

<div class="container-fluid">
    <div class="row">
        @include('components.employee-sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 main-content px-4">

            <div class="section-content" id="dashboard-section">
                <div class="welcome-section mb-4 pt-3">
                    <h2 id="welcome-name" class="fw-bold">Cargando...</h2>
                    <p class="text-muted">Panel de control operativo de la panadería</p>
                </div>

                <div class="row g-3 mb-4" id="stats-row">
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card p-3 shadow-sm rounded">
                            <div class="d-flex align-items-center">
                                <div class="card-icon bg-primary-light text-primary p-3 rounded-circle me-3">
                                    <i class="bi bi-cart-check fs-4"></i>
                                </div>
                                <div>
                                    <div class="stat-number fw-bold fs-4">{{ $pedidosHoy ?? 0 }}</div>
                                    <div class="stat-label text-muted small">Pedidos Hoy</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card p-3 shadow-sm rounded">
                            <div class="d-flex align-items-center">
                                <div class="card-icon bg-warning-light text-warning p-3 rounded-circle me-3">
                                    <i class="bi bi-clock fs-4"></i>
                                </div>
                                <div>
                                    <div class="stat-number fw-bold fs-4">{{ $pedidosPendientes ?? 0 }}</div>
                                    <div class="stat-label text-muted small">Pendientes</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card p-3 shadow-sm rounded">
                            <div class="d-flex align-items-center">
                                <div class="card-icon bg-info-light text-info p-3 rounded-circle me-3">
                                    <i class="bi bi-box-seam fs-4"></i>
                                </div>
                                <div>
                                    <div class="stat-number fw-bold fs-4">{{ $productosDisponibles ?? 0 }}</div>
                                    <div class="stat-label text-muted small">Productos</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card p-3 shadow-sm rounded">
                            <div class="d-flex align-items-center">
                                <div class="card-icon bg-success-light text-success p-3 rounded-circle me-3">
                                    <i class="bi bi-check-circle fs-4"></i>
                                </div>
                                <div>
                                    <div class="stat-number fw-bold fs-4">{{ $totalPedidos ?? 0 }}</div>
                                    <div class="stat-label text-muted small">Total Pedidos</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="orders-section" id="main-actions">
                    <div class="section-header mb-3 border-bottom pb-2">
                        <h4 class="fw-bold"><i class="fas fa-bolt text-warning me-2"></i>Acciones Rápidas</h4>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="action-card text-center p-5 shadow-sm">
                                <div class="mb-3">
                                    <i class="bi bi-plus-circle-fill fs-1 text-primary"></i>
                                </div>
                                <h4 class="fw-bold">Crear Pedido</h4>
                                <p class="text-muted">Inicia una nueva orden de venta para un cliente.</p>
                                <a href="{{ route('pedidos.create') }}" class="btn btn-primary btn-lg w-100 mt-2" style="background: #a67c52; border: none;">
                                    <i class="fas fa-plus me-2"></i>Nueva Orden
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="action-card text-center p-5 shadow-sm">
                                <div class="mb-3">
                                    <i class="bi bi-list-check fs-1 text-success"></i>
                                </div>
                                <h4 class="fw-bold">Ver Pedidos</h4>
                                <p class="text-muted">Gestiona, edita o cancela los pedidos existentes.</p>
                                <a href="{{ route('pedidos.index') }}" class="btn btn-success btn-lg w-100 mt-2">
                                    <i class="fas fa-search me-2"></i>Ver Listado
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-content-inner mt-4" id="pedidos-section" style="display: none;">
                    <div class="alert alert-info">Redirigiendo al listado de pedidos...</div>
                </div>

            </div>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Verificación de Seguridad y Sesión
        if (AuthManager.wasLoggedOut()) {
            AuthManager.clearAuth();
            window.location.replace('/login');
            return;
        }

        if (!AuthManager.isAuthenticated()) {
            AuthManager.redirectToLogin();
            return;
        }

        const userData = AuthManager.getUserData();
        const userRole = AuthManager.getRole();

        // 2. Control de Acceso por Rol
        if (userRole !== 'EMPLEADO') {
            const correctDashboard = AuthManager.getDashboardRoute(userRole);
            window.location.href = correctDashboard;
            return;
        }

        // 3. Personalización del nombre
        const welcomeTitle = document.getElementById('welcome-name');
        if (userData && (userData.nombre || userData.name)) {
            welcomeTitle.innerText = `¡Hola, ${userData.nombre || userData.name}`;
        } else {
            welcomeTitle.innerText = `¡Bienvenido, Empleado!`;
        }

        // 4. Lógica de navegación del Sidebar (Corregida para manejar vistas externas)
        const navLinks = document.querySelectorAll('.nav-link');
        const sections = document.querySelectorAll('.section-content-inner');
        const dashboardMain = document.getElementById('main-actions');
        const statsMain = document.getElementById('stats-row');

        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');

                // Si es un enlace interno (#), manejamos la visibilidad
                if (href && href.startsWith('#')) {
                    e.preventDefault();

                    navLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');

                    // Ocultar todo
                    sections.forEach(s => s.style.display = 'none');
                    
                    if (href === '#inicio') {
                        dashboardMain.style.display = 'block';
                        statsMain.style.display = 'flex';
                    } else {
                        dashboardMain.style.display = 'none';
                        statsMain.style.display = 'none';
                        const sectionId = href.substring(1) + '-section';
                        const targetSection = document.getElementById(sectionId);
                        if (targetSection) targetSection.style.display = 'block';
                    }
                }
                // Si el href es una ruta de Laravel (como pedidos.index), el navegador hará la carga normal.
            });
        });
    });
</script>
@endpush
