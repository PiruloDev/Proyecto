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
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar">
            <div class="sidebar-content">
                <div class="sidebar-brand">
                    <img src="{{ asset('images/logoprincipal.jpg') }}" alt="Logo Panadería" class="sidebar-logo">
                    <h5>Portal Administrador</h5>
                </div>

                <div class="sidebar-divider"></div>

                <ul class="nav flex-column">
                    <div class="nav-item">
                        <a class="nav-link active" href="{{ route('dashboard.admin') }}">
                            <i class="bi bi-house-door"></i>
                            Dashboard
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard.inventario') }}">
                            <i class="bi bi-boxes"></i>
                            Producción
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" href="{{ route('pedidos.index') }}">
                            <i class="bi bi-cart-check"></i>
                            Pedidos
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-people"></i>
                            Empleados
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-graph-up"></i>
                            Estadísticas
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-box-seam"></i>
                            Productos
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-gear"></i>
                            Ajustes
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

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
            <!-- Mobile menu button -->
            <button class="btn btn-outline-primary d-md-none mb-3" type="button" id="sidebarToggle">
                <i class="bi bi-list"></i> Menú
            </button>

            <div class="d-flex justify-content-center flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2">Gestion de Panadería</h1>
            </div>
            <!-- Dashboard Cards Grid -->
            <div class="row g-4 mb-4">
                <!-- Card: Producción -->
                <div class="col-md-6 col-lg-4">
                    <div class="card glass-card border-0 rounded-6 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-box-seam"></i>
                            <h5>Producción</h5>
                            <p class="text-muted">Gestiona ingredientes y recetas</p>
                            <div class="d-flex justify-content-center">
                                <a href="{{ route('dashboard.inventario') }}" class="btn" style="background: var(--panaderia-marron-principal); color: white; border-radius: var(--panaderia-radius-md);">
                                    Ir a Producción <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Pedidos -->
                <div class="col-md-6 col-lg-4">
                    <div class="card glass-card border-0 rounded-4 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-cart-check"></i>
                            <h5>Pedidos</h5>
                            <p class="text-muted">Administra pedidos de clientes</p>
                            <div class="d-flex justify-content-center">
                                <a href="{{ route('pedidos.index') }}" class="btn" style="background: var(--panaderia-marron-principal); color: white; border-radius: var(--panaderia-radius-md);">
                                    Ver Pedidos <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Empleados -->
                <div class="col-md-6 col-lg-4">
                    <div class="card glass-card border-0 rounded-4 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-people"></i>
                            <h5>Empleados</h5>
                            <p class="text-muted">Gestión de personal</p>
                            <div class="mt-3">
                                <span class="badge bg-secondary">Próximamente</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Estadísticas -->
                <div class="col-md-6 col-lg-4">
                    <div class="card glass-card border-0 rounded-4 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-graph-up"></i>
                            <h5>Estadísticas</h5>
                            <p class="text-muted">Reportes y análisis</p>
                            <div class="mt-3">
                                <span class="badge bg-secondary">Próximamente</span>
                            </div>
                        </div>
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
