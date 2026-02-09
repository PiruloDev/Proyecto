@extends('layouts.app')

@section('title', 'Dashboard Cliente - Panadería')

@push('styles')
<link href="{{ asset('css/variables.css') }}" rel="stylesheet">
<link href="{{ asset('css/dashboard-client.css') }}" rel="stylesheet">
<style>
    
    .sidebar {
        display: flex;
        flex-direction: column;
        height: 100vh;
        background-color: #8d734d; 
    }

    .sidebar-content {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .sidebar-user {
        margin-top: auto; 
        padding: 20px 15px;
        background: rgba(0, 0, 0, 0.15); 
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .logout-btn-custom {
        color: #ffbaba !important; 
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 186, 186, 0.2);
        padding: 10px 15px;
        border-radius: 8px;
        width: 100%;
        text-align: left;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        text-decoration: none;
    }

    .logout-btn-custom:hover {
        background: rgba(255, 186, 186, 0.15);
        color: #ffffff !important;
    }

    /* 2. Diseño de Precios y Tarjetas */
    .order-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .price-badge {
        background-color: #f0f7ff;
        color: #0d6efd;
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 1rem;
    }

    .order-card {
        transition: transform 0.2s;
        border-radius: 12px;
        border: 1px solid #eee;
    }
    
    .order-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
</style>
@endpush

@section('content')

@php
    $pedidosCol = collect($pedidos ?? []);
    $totalPedidos = $pedidosCol->count();
    $pedidosPendientes = $pedidosCol->filter(function($p) {
        $estadoId = $p['ID_ESTADO_PEDIDO'] ?? $p['id_ESTADO_PEDIDO'] ?? 0;
        return (int)$estadoId === 1;
    })->count();

    $pedidosRecientes = $pedidosCol->take(5);

    if (!function_exists('formatApiDate')) {
        function formatApiDate($dateString) {
            if (empty($dateString) || $dateString == 'N/A') return 'Pendiente';
            try {
                return \Carbon\Carbon::parse($dateString)->format('d/m/Y h:i A');
            } catch (\Exception $e) { return $dateString; }
        }
    }
@endphp

<div class="container-fluid">
    <div class="row">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <button class="btn btn-hamburger d-md-none" type="button" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>

        <nav class="col-md-3 col-lg-2 d-md-block sidebar">
            <div class="sidebar-content">
                <div class="sidebar-brand">
                    <img src="{{ asset('images/logoprincipal.jpg') }}" alt="Logo" class="sidebar-logo">
                    <h5 class="text-white">Portal Cliente</h5>
                </div>

                <div class="px-3 mb-3">
                    <a href="{{ route('menu') }}" class="btn btn-explore w-100">
                        <i class="bi bi-compass"></i> Explorar Productos
                    </a>
                </div>

                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#dashboard" data-section="dashboard">
                            <i class="bi bi-house"></i> Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pedidos" data-section="pedidos">
                            <i class="bi bi-cart-check"></i> Mis Pedidos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#detalles-general" data-section="detalles-general">
                            <i class="bi bi-journal-text"></i> Detalle de Pedidos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#mi-cuenta" data-section="mi-cuenta">
                            <i class="bi bi-person-circle"></i> Mi Perfil
                        </a>
                    </li>
                </ul>

                <div class="sidebar-user">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-person-circle fs-3 text-white me-2"></i>
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-white lh-1">{{ session('usuario.nombre', 'Usuario') }}</span>
                            <small class="text-white-50">Cliente</small>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="logout-btn-custom btn">
                            <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
            
            <div class="section-content" id="dashboard-section" style="display: block;">
                <div class="welcome-section">
                    <h2>Bienvenido, {{ session('usuario.nombre', 'Cliente') }}</h2>
                    <p>Resumen de actividad para {{ $totalPedidos }} pedidos.</p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="card-icon"><i class="bi bi-cart-check"></i></div>
                        <div class="card-content">
                            <div class="stat-number">{{ $totalPedidos }}</div>
                            <div class="stat-label">Total Pedidos</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="card-icon"><i class="bi bi-clock"></i></div>
                        <div class="card-content">
                            <div class="stat-number">{{ $pedidosPendientes }}</div>
                            <div class="stat-label">Pendientes</div>
                        </div>
                    </div>
                </div>

                <div class="orders-section mt-4">
                    <h4 class="mb-3"><i class="bi bi-receipt"></i> Pedidos Recientes</h4>
                    @forelse($pedidosRecientes as $pedido)
                        <div class="order-item shadow-sm mb-3 p-3 border-0 rounded-3 bg-white">
                            <div class="order-item-header">
                                <div>
                                    <h6 class="fw-bold mb-0">Pedido #{{ $pedido['id_PEDIDO'] ?? $pedido['ID_PEDIDO'] }}</h6>
                                    <small class="text-muted">{{ formatApiDate($pedido['fecha_INGRESO'] ?? null) }}</small>
                                </div>
                                <div class="price-badge">${{ number_format($pedido['total_PRODUCTO'] ?? 0, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">No hay pedidos recientes.</p>
                    @endforelse
                </div>
            </div>

            <div class="section-content" id="pedidos-section" style="display: none;">
                <h3 class="mb-4">Historial de Pedidos</h3>
                <div class="table-responsive">
                    <table class="table table-hover bg-white shadow-sm rounded">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Fecha Ingreso</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Fecha Entrega</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pedidosCol as $p)
                            <tr>
                                <td>#{{ $p['id_PEDIDO'] ?? $p['ID_PEDIDO'] }}</td>
                                <td>{{ formatApiDate($p['fecha_INGRESO'] ?? null) }}</td>
                                <td class="fw-bold">${{ number_format($p['total_PRODUCTO'] ?? 0, 0, ',', '.') }}</td>
                                <td><span class="badge bg-info">Recibido</span></td>
                                <td>{{ formatApiDate($p['fecha_ENTREGA'] ?? null) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="section-content" id="detalles-general-section" style="display: none;">
                <h3 class="mb-4">Detalle de Productos por Pedido</h3>
                @foreach($pedidosCol as $pedido)
                    <div class="card order-card shadow-sm mb-4 border-0">
                        <div class="card-header bg-primary text-white d-flex justify-content-between">
                            <span class="fw-bold"><i class="bi bi-box-seam me-2"></i>Pedido #{{ $pedido['id_PEDIDO'] ?? $pedido['ID_PEDIDO'] }}</span>
                            <span>{{ formatApiDate($pedido['fecha_INGRESO'] ?? null) }}</span>
                        </div>
                        <div class="card-body p-0">
                            <table class="table detail-table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">PRODUCTO</th>
                                        <th class="text-center">CANTIDAD</th>
                                        <th class="text-end">PRECIO UNIT.</th>
                                        <th class="text-end pe-4">SUBTOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $items = collect($pedido['productos'] ?? []); @endphp
                                    @foreach($items as $item)
                                    <tr>
                                        <td class="ps-4"><i class="bi bi-dot text-primary fs-4"></i>{{ $item['nombre'] }}</td>
                                        <td class="text-center">{{ $item['cantidad'] }}</td>
                                        <td class="text-end">${{ number_format($item['precio'], 0, ',', '.') }}</td>
                                        <td class="text-end pe-4 fw-bold text-primary">${{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-light text-end pe-4">
                            <span class="text-muted me-2">Monto Total:</span>
                            <span class="h5 mb-0 fw-bold">${{ number_format($pedido['total_PRODUCTO'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="section-content" id="mi-cuenta-section" style="display: none;">
                <div class="card border-0 shadow-sm p-4">
                    <h3>Mi Perfil</h3>
                    <hr>
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center">
                            <i class="bi bi-person-circle" style="font-size: 6rem; color: #dee2e6;"></i>
                        </div>
                        <div class="col-md-8">
                            <p class="mb-1 text-muted">Nombre completo</p>
                            <h5 class="text-capitalize mb-3">{{ session('usuario.nombre') }}</h5>
                            <p class="mb-1 text-muted">Correo de contacto</p>
                            <h5 class="mb-3">{{ session('usuario.email') }}</h5>
                            <span class="badge bg-secondary">CLIENTE REGISTRADO</span>
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

        function showSection(sectionId) {
            sections.forEach(s => s.style.display = 'none');
            const target = document.getElementById(sectionId + '-section');
            if (target) {
                target.style.display = 'block';
                target.style.opacity = 0;
                setTimeout(() => { target.style.opacity = 1; target.style.transition = 'opacity 0.3s'; }, 10);
            }
        }

        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if(!href || !href.startsWith('#')) return;
                e.preventDefault();
                const sectionName = this.getAttribute('data-section');
                navLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                showSection(sectionName);
            });
        });

       
        const toggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if(toggle) toggle.onclick = () => { sidebar.classList.toggle('show'); overlay.classList.toggle('show'); };
        if(overlay) overlay.onclick = () => { sidebar.classList.remove('show'); overlay.classList.remove('show'); };
    });
</script>
@endpush