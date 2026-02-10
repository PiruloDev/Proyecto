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
        @include('components.employee-sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 main-content">

            <div class="section-content" id="dashboard-section">
                <div class="welcome-section mb-4">
                    {{-- Cambiamos el texto estático por el nombre del usuario --}}
                    <h2 id="welcome-name">¡Bienvenido, Carga...!</h2>
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
                                <a href="{{ route('pedidos.create') }}" class="btn btn-primary w-100">Crear</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="action-card text-center p-4">
                                <i class="bi bi-list-check fs-1 text-success mb-3"></i>
                                <h5>Ver Pedidos</h5>
                                <p class="text-muted">Revisar pedidos pendientes</p>
                                <a href="{{ route('pedidos.index') }}" class="btn btn-success w-100">Ver</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Secciones de navegación --}}
                <div class="section-content-inner" id="pedidos-section" style="display: none;">
                    <h3>Gestión de Pedidos</h3>
                    <p>Contenido para gestionar pedidos...</p>
                </div>

                <div class="section-content-inner" id="productos-section" style="display: none;">
                    <h3>Productos</h3>
                    <p>Contenido para visualizar productos disponibles.</p>
                </div>

                <div class="section-content-inner" id="perfil-section" style="display: none;">
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

        // Verificación de Rol
        if (userRole !== 'EMPLEADO') {
            const correctDashboard = AuthManager.getDashboardRoute(userRole);
            window.location.href = correctDashboard;
            return;
        }

        // --- DINAMISMO DEL NOMBRE ---
        // Aquí insertamos el nombre del usuario en el H2
        const welcomeTitle = document.getElementById('welcome-name');
        if (userData && (userData.nombre || userData.name)) {
            welcomeTitle.innerText = `¡Bienvenido, ${userData.nombre || userData.name}!`;
        } else {
            welcomeTitle.innerText = `¡Bienvenido, Empleado!`;
        }

        console.log('Dashboard Empleado Cargado:', userData);

        // Navegación por secciones
        const navLinks = document.querySelectorAll('.nav-link');
        const sections = document.querySelectorAll('.section-content-inner');

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

                    // Mostramos el dashboard solo si es la raíz o si no hay sección específica
                    const dashboardMain = document.querySelector('.orders-section');
                    const statsMain = document.querySelector('.row.g-3.mb-4');

                    if (href === '#inicio') {
                        dashboardMain.style.display = 'block';
                        statsMain.style.display = 'flex';
                    } else if (targetSection) {
                        dashboardMain.style.display = 'none';
                        statsMain.style.display = 'none';
                        targetSection.style.display = 'block';
                    }
                }
            });
        });
    });
</script>
@endpush
