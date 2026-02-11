@extends('layouts.app')

@section('title', 'Dashboard Administrador - Panadería')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="{{ asset('css/variables.css') }}" rel="stylesheet">
<link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet">
@endpush

@section('content')
<script>
    // Verificación inmediata antes de renderizar
    (function() {
        const logoutFlag = sessionStorage.getItem('logout_flag');
        if (logoutFlag === 'true') {
            // Limpiar todo el sessionStorage
            sessionStorage.clear();
            window.location.replace('/login');
        }
    })();
</script>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Component -->
        @include('components.admin-sidebar')

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4 main-content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2">Gestión de Panadería</h1>
            </div>

            <!-- Resumen General -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #a67c52 0%, #8b6745 100%); border-radius: 12px;">
                        <div class="card-body text-center text-white py-3">
                            <i class="bi bi-cart-check" style="font-size: 2.5rem;"></i>
                            <h2 class="mt-2 mb-0">12</h2>
                            <p class="mb-0 small">Pedidos Hoy</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #a67c52 0%, #8b6745 100%); border-radius: 12px;">
                        <div class="card-body text-center text-white py-3">
                            <i class="bi bi-box-seam" style="font-size: 2.5rem;"></i>
                            <h2 class="mt-2 mb-0">48</h2>
                            <p class="mb-0 small">Productos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #a67c52 0%, #8b6745 100%); border-radius: 12px;">
                        <div class="card-body text-center text-white py-3">
                            <i class="bi bi-exclamation-triangle" style="font-size: 2.5rem;"></i>
                            <h2 class="mt-2 mb-0">5</h2>
                            <p class="mb-0 small">Alertas Stock</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #a67c52 0%, #8b6745 100%); border-radius: 12px;">
                        <div class="card-body text-center text-white py-3">
                            <i class="bi bi-currency-dollar" style="font-size: 2.5rem;"></i>
                            <h2 class="mt-2 mb-0">$450K</h2>
                            <p class="mb-0 small">Ventas del Mes</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Cards Grid -->
            <div class="row g-3 mb-4">
                <!-- Card: Producción -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                        <div class="card-body text-center py-4">
                            <i class="bi bi-boxes" style="font-size: 3rem; color: #a67c52;"></i>
                            <h5 class="mt-3 mb-2">Producción</h5>
                            <p class="text-muted small mb-3">Ingredientes y recetas</p>
                            <a href="{{ route('dashboard.inventario') }}" class="btn btn-sm" style="background: #a67c52; color: white; border-radius: 8px; padding: 8px 20px;">
                                Acceder <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card: Pedidos -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                        <div class="card-body text-center py-4">
                            <i class="bi bi-cart-check" style="font-size: 3rem; color: #a67c52;"></i>
                            <h5 class="mt-3 mb-2">Pedidos</h5>
                            <p class="text-muted small mb-3">Gestión de pedidos</p>
                            <a href="{{ route('admin.pedidos.index') }}" class="btn btn-sm" style="background: #a67c52; color: white; border-radius: 8px; padding: 8px 20px;">
                                Acceder <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card: Productos -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                        <div class="card-body text-center py-4">
                            <i class="bi bi-box-seam" style="font-size: 3rem; color: #a67c52;"></i>
                            <h5 class="mt-3 mb-2">Productos</h5>
                            <p class="text-muted small mb-3">Catálogo de productos</p>
                            <a href="{{ route('productos.index') }}" class="btn btn-sm" style="background: #a67c52; color: white; border-radius: 8px; padding: 8px 20px;">
                                Acceder <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card: Empleados -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                        <div class="card-body text-center py-4">
                            <i class="bi bi-people" style="font-size: 3rem; color: #a67c52;"></i>
                            <h5 class="mt-3 mb-2">Empleados</h5>
                            <p class="text-muted small mb-3">Gestión de personal</p>
                            <a href="{{ route('empleados.index') }}" class="btn btn-sm" style="background: #a67c52; color: white; border-radius: 8px; padding: 8px 20px;">
                                Acceder <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card: Clientes -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                        <div class="card-body text-center py-4">
                            <i class="bi bi-person-badge" style="font-size: 3rem; color: #a67c52;"></i>
                            <h5 class="mt-3 mb-2">Clientes</h5>
                            <p class="text-muted small mb-3">Base de clientes</p>
                            <a href="{{ route('clientes.index') }}" class="btn btn-sm" style="background: #a67c52; color: white; border-radius: 8px; padding: 8px 20px;">
                                Acceder <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card: Estadísticas -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                        <div class="card-body text-center py-4">
                            <i class="bi bi-graph-up" style="font-size: 3rem; color: #a67c52;"></i>
                            <h5 class="mt-3 mb-2">Estadísticas</h5>
                            <p class="text-muted small mb-3">Reportes y análisis</p>
                            <a href="{{ route('ordenes.salida.index') }}" class="btn btn-sm" style="background: #a67c52; color: white; border-radius: 8px; padding: 8px 20px;">
                                Acceder <i class="bi bi-arrow-right"></i>
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
        // Verificar si se hizo logout explícito
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

        if (userRole !== 'ADMIN' && userRole !== 'ADMINISTRADOR') {
            console.warn('Usuario no autorizado para dashboard admin');
            const correctDashboard = AuthManager.getDashboardRoute(userRole);
            window.location.href = correctDashboard;
            return;
        }

        console.log('Dashboard Admin - Usuario autenticado:', userData);
    });
</script>
@endpush
