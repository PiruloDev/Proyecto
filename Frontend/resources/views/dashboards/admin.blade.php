@extends('layouts.app')

@section('title', 'Dashboard Administrador - Panadería')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="{{ asset('css/variables.css') }}" rel="stylesheet">
<link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Component -->
        @include('components.admin-sidebar')
        
        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2">Gestión de Panadería</h1>
            </div>
            
            <!-- Dashboard Cards Grid -->
            <div class="row g-4 mb-4">
                <!-- Card: Producción -->
                <div class="col-md-6 col-lg-4">
                    <div class="card glass-card border-0 rounded-4 shadow-sm h-100">
                        <div class="card-body text-center d-flex flex-column">
                            <i class="bi bi-boxes" style="font-size: 3.5rem; color: var(--panaderia-marron-principal);"></i>
                            <h5 class="mt-3">Producción</h5>
                            <p class="text-muted flex-grow-1">Gestiona ingredientes y recetas de producción</p>
                            <div class="d-flex justify-content-center mt-auto">
                                <a href="{{ route('dashboard.inventario') }}" class="btn" style="background: var(--panaderia-marron-principal); color: white; border-radius: var(--panaderia-radius-md);">
                                    Ir a Producción <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Pedidos -->
                <div class="col-md-6 col-lg-4">
                    <div class="card glass-card border-0 rounded-4 shadow-sm h-100">
                        <div class="card-body text-center d-flex flex-column">
                            <i class="bi bi-cart-check" style="font-size: 3.5rem; color: var(--panaderia-marron-principal);"></i>
                            <h5 class="mt-3">Pedidos</h5>
                            <p class="text-muted flex-grow-1">Administra y gestiona pedidos de clientes</p>
                            <div class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.pedidos.*') ? 'active' : '' }}"
       href="{{ route('admin.pedidos.index') }}">
        <i class="bi bi-cart-check"></i>
        Pedidos
    </a>
</div>
                        </div>
                    </div>
                </div>

                <!-- Card: Productos -->
                <div class="col-md-6 col-lg-4">
                    <div class="card glass-card border-0 rounded-4 shadow-sm h-100">
                        <div class="card-body text-center d-flex flex-column">
                            <i class="bi bi-box-seam" style="font-size: 3.5rem; color: var(--panaderia-marron-principal);"></i>
                            <h5 class="mt-3">Productos</h5>
                            <p class="text-muted flex-grow-1">CRUD completo para gestión de productos</p>
                            <div class="d-flex justify-content-center mt-auto">
                                <a href="{{ route('productos.index') }}" class="btn" style="background: var(--panaderia-marron-principal); color: white; border-radius: var(--panaderia-radius-md);">
                                    Gestionar Productos <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Empleados -->
                <div class="col-md-6 col-lg-4">
                    <div class="card glass-card border-0 rounded-4 shadow-sm h-100">
                        <div class="card-body text-center d-flex flex-column">
                            <i class="bi bi-people" style="font-size: 3.5rem; color: var(--panaderia-marron-principal);"></i>
                            <h5 class="mt-3">Empleados</h5>
                            <p class="text-muted flex-grow-1">Gestión y administración de personal</p>
                            <div class="d-flex justify-content-center mt-auto">
                                <a href="{{ route('empleados.index') }}" class="btn" style="background: var(--panaderia-marron-principal); color: white; border-radius: var(--panaderia-radius-md);">
                                    Gestionar Empleados <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Clientes -->
                <div class="col-md-6 col-lg-4">
                    <div class="card glass-card border-0 rounded-4 shadow-sm h-100">
                        <div class="card-body text-center d-flex flex-column">
                            <i class="bi bi-person-badge" style="font-size: 3.5rem; color: var(--panaderia-marron-principal);"></i>
                            <h5 class="mt-3">Clientes</h5>
                            <p class="text-muted flex-grow-1">Base de datos de clientes</p>
                            <div class="d-flex justify-content-center mt-auto">
                                <a href="{{ route('clientes.index') }}" class="btn" style="background: var(--panaderia-marron-principal); color: white; border-radius: var(--panaderia-radius-md);">
                                    Ver Clientes <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Estadísticas -->
                <div class="col-md-6 col-lg-4">
                    <div class="card glass-card border-0 rounded-4 shadow-sm h-100">
                        <div class="card-body text-center d-flex flex-column">
                            <i class="bi bi-person-badge" style="font-size: 3.5rem; color: var(--panaderia-marron-principal);"></i>
                            <h5 class="mt-3">Estadísticas</h5>
                            <p class="text-muted flex-grow-1">Reportes y análisis de ventas</p>
                            <div class="d-flex justify-content-center mt-auto">
                                <a href="{{ route('ordenes.salida.index') }}" class="btn" style="background: var(--panaderia-marron-principal); color: white; border-radius: var(--panaderia-radius-md);">
                                    Gestionar estadísticas <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>


            <!-- Resumen General -->
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="card glass-card border-0 rounded-4 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="bi bi-speedometer2"></i> Resumen General
                            </h5>
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="p-3">
                                        <i class="bi bi-cart-check-fill fs-1 text-success"></i>
                                        <h3 class="mt-2">12</h3>
                                        <p class="text-muted mb-1"><strong>Pedidos Hoy</strong></p>
                                        <small class="text-muted">Pedidos recibidos el día de hoy</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3">
                                        <i class="bi bi-box-seam-fill fs-1 text-primary"></i>
                                        <h3 class="mt-2">48</h3>
                                        <p class="text-muted mb-1"><strong>Productos en Catálogo</strong></p>
                                        <small class="text-muted">Total de productos activos</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3">
                                        <i class="bi bi-exclamation-triangle-fill fs-1 text-warning"></i>
                                        <h3 class="mt-2">5</h3>
                                        <p class="text-muted mb-1"><strong>Alertas de Stock</strong></p>
                                        <small class="text-muted">Productos con inventario bajo</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3">
                                        <i class="bi bi-currency-dollar fs-1 text-info"></i>
                                        <h3 class="mt-2">$450K</h3>
                                        <p class="text-muted mb-1"><strong>Ventas del Mes</strong></p>
                                        <small class="text-muted">Total acumulado de {{ date('F Y') }}</small>
                                    </div>
                                </div>
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
        // Toggle sidebar en móvil si tienes botón
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
        }
    });
</script>
@endpush