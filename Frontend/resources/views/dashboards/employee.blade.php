@extends('layouts.app')

@section('title', 'Dashboard Empleado - Panadería')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/stylempleado.css') }}">
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
                    <h5>Portal Empleado</h5>
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
                        <a class="nav-link" href="#pedidos">
                            <i class="bi bi-cart-check"></i>
                            Gestionar Pedidos
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" href="#productos">
                            <i class="bi bi-box-seam"></i>
                            Ver Productos
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" href="#perfil">
                            <i class="bi bi-person-circle"></i>
                            Mi Perfil
                        </a>
                    </div>
                </ul>
                
                <div class="sidebar-divider"></div>
                
                <div class="sidebar-user">
                    <div class="user-info">
                        <i class="bi bi-person-circle"></i>
                        <span>{{ Auth::user()->name }}</span>
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
                <div class="welcome-section">
                    <h2>Bienvenido, {{ Auth::user()->name }}!</h2>
                    <p>Panel de control del empleado</p>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="card-icon">
                            <i class="bi bi-cart-check"></i>
                        </div>
                        <div class="card-content">
                            <div class="stat-number">{{ $pedidosHoy ?? 0 }}</div>
                            <div class="stat-label">Pedidos Hoy</div>
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
                    <div class="stat-card">
                        <div class="card-icon">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div class="card-content">
                            <div class="stat-number">{{ $productosDisponibles ?? 0 }}</div>
                            <div class="stat-label">Productos Disponibles</div>
                        </div>
                    </div>
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

                <!-- Quick Actions -->
                <div class="orders-section mt-4">
                    <div class="section-header">
                        <h4><i class="bi bi-lightning"></i> Acciones Rápidas</h4>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="action-card">
                                <i class="bi bi-plus-circle"></i>
                                <h5>Crear Pedido</h5>
                                <p>Registrar un nuevo pedido</p>
                                <button class="btn btn-primary">Crear</button>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="action-card">
                                <i class="bi bi-list-check"></i>
                                <h5>Ver Pedidos</h5>
                                <p>Revisar pedidos pendientes</p>
                                <button class="btn btn-primary">Ver</button>
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
