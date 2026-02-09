@extends('layouts.app')

@section('title', 'Dashboard Cliente - Panadería')

@push('styles')
<link href="{{ asset('css/variables.css') }}" rel="stylesheet">
<link href="{{ asset('css/dashboard-client.css') }}" rel="stylesheet">
<style>
    .order-card {
        transition: transform 0.2s;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #eee;
    }
    .order-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .detail-table thead th {
        background-color: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        padding: 12px;
    }
    .logout-btn {
        color: #dc3545;
        transition: all 0.3s;
        padding: 8px 12px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        width: 100%;
    }
    .logout-btn:hover {
        background: rgba(220, 53, 69, 0.1);
        color: #a71d2a;
    }
    .empty-state-icon {
        font-size: 2.5rem;
        color: #dee2e6;
        display: block;
        margin-bottom: 10px;
    }
</style>
@endpush

@section('content')

@php
    // Convertimos a colección para asegurar el uso de métodos de Laravel
    $pedidosCol = collect($pedidos ?? []);
    $totalPedidos = $pedidosCol->count();
    $pedidosPendientes = 0;

    foreach ($pedidosCol as $pedido) {
        // Buscamos el ID de estado en múltiples formatos por si la API varía
        $estadoId = $pedido['ID_ESTADO_PEDIDO'] ?? $pedido['id_ESTADO_PEDIDO'] ?? $pedido['estado_pedido_id'] ?? 0;
        if ((int)$estadoId === 1) {
            $pedidosPendientes++;
        }
    }

    $pedidosRecientes = $pedidosCol->take(5);

    if (!function_exists('formatApiDate')) {
        function formatApiDate($dateString) {
            if (empty($dateString) || $dateString == 'N/A') return 'Pendiente';
            try {
                return \Carbon\Carbon::parse($dateString)->format('d/m/Y h:i A');
            } catch (\Exception $e) {
                return $dateString;
            }
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
                <button class="btn-close-sidebar d-md-none" id="sidebarClose">
                    <i class="bi bi-x-lg"></i>
                </button>
                <div class="sidebar-brand">
                    <img src="{{ asset('images/logoprincipal.jpg') }}" alt="Logo Panadería" class="sidebar-logo">
                    <h5>Portal Cliente</h5>
                </div>
                <div class="px-3 mb-3">
                    <a href="{{ route('menu') }}" class="btn btn-explore w-100">
                        <i class="bi bi-compass"></i> Explorar Productos
                    </a>
                </div>
                <div class="sidebar-divider"></div>

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
                            <i class="bi bi-journal-text"></i> Desglose Total
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#mi-cuenta" data-section="mi-cuenta">
                            <i class="bi bi-person-circle"></i> Mi Perfil
                        </a>
                    </li>
                </ul>

                <div class="sidebar-divider"></div>

                <div class="sidebar-user">
                    <div class="user-info mb-2">
                        <i class="bi bi-person-circle fs-4 me-2"></i>
                        <div class="d-flex flex-column">
                            <span class="fw-bold">{{ session('usuario.nombre', 'Usuario') }}</span>
                            <small class="text-muted">Cliente</small>
                        </div>
                    </div>
                    
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="logout-btn btn btn-link text-decoration-none text-start">
                            <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="section-content" id="dashboard-section" style="display: block;">
                <div class="welcome-section">
                    <h2>Bienvenido, {{ session('usuario.nombre', 'Cliente') }}</h2>
                    <p>Resumen de actividad para {{ $totalPedidos }} pedidos registrados.</p>
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
                    <h4><i class="bi bi-receipt"></i> Pedidos Recientes</h4>
                    @forelse($pedidosRecientes as $pedido)
                        <div class="order-item shadow-sm mb-3 p-3 border rounded bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold mb-1">Pedido #{{ $pedido['id_PEDIDO'] ?? $pedido['idPedido'] ?? $pedido['ID_PEDIDO'] }}</h6>
                                    <small class="text-muted">{{ formatApiDate($pedido['fecha_INGRESO'] ?? $pedido['fecha_ingreso'] ?? null) }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-primary">${{ number_format($pedido['total_PRODUCTO'] ?? $pedido['total_producto'] ?? 0, 0) }}</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 bg-light rounded">
                            <p class="text-muted mb-0">No hay pedidos recientes.</p>
                        </div>
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
                                <td>#{{ $p['id_PEDIDO'] ?? $p['idPedido'] ?? $p['ID_PEDIDO'] }}</td>
                                <td>{{ formatApiDate($p['fecha_INGRESO'] ?? $p['fecha_ingreso'] ?? null) }}</td>
                                <td>${{ number_format($p['total_PRODUCTO'] ?? $p['total_producto'] ?? 0, 0) }}</td>
                                <td><span class="badge bg-info">Recibido</span></td>
                                <td>{{ formatApiDate($p['fecha_ENTREGA'] ?? $p['fecha_entrega'] ?? null) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="section-content" id="detalles-general-section" style="display: none;">
                <h3 class="mb-4">Desglose de Productos por Pedido</h3>
                
                @forelse($pedidosCol as $pedido)
                    <div class="card order-card shadow-sm mb-4 border-0">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <span class="fw-bold">
                                <i class="bi bi-box-seam me-2"></i>Pedido #{{ $pedido['id_PEDIDO'] ?? $pedido['idPedido'] ?? $pedido['ID_PEDIDO'] }}
                            </span>
                            <span class="badge bg-light text-primary">
                                {{ formatApiDate($pedido['fecha_INGRESO'] ?? $pedido['fecha_ingreso'] ?? null) }}
                            </span>
                        </div>
                        <div class="card-body p-0">
                            <table class="table detail-table mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Producto</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-end">Precio Unit.</th>
                                        <th class="text-end pe-4">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php 
                                        // Extraemos los productos ya procesados por el DashboardController
                                        $items = collect($pedido['productos'] ?? []); 
                                    @endphp
                                    
                                    @forelse($items as $item)
                                    <tr>
                                        <td class="ps-4">
                                            <i class="bi bi-dot text-primary fs-4"></i>
                                            {{ $item['nombre'] ?? 'Producto Desconocido' }}
                                        </td>
                                        <td class="text-center">{{ $item['cantidad'] ?? 0 }}</td>
                                        <td class="text-end">${{ number_format($item['precio'] ?? 0, 0) }}</td>
                                        <td class="text-end pe-4 fw-bold text-primary">
                                            ${{ number_format($item['subtotal'] ?? 0, 0) }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="bi bi-info-circle empty-state-icon"></i>
                                            No se encontraron productos detallados para este pedido.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-light text-end pe-4">
                            <span class="text-muted me-2">Monto Total:</span>
                            <span class="h5 mb-0 fw-bold text-dark">${{ number_format($pedido['total_PRODUCTO'] ?? $pedido['total_producto'] ?? 0, 0) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info border-0 shadow-sm">
                        <i class="bi bi-info-circle me-2"></i>Aún no tienes pedidos para mostrar.
                    </div>
                @endforelse
            </div>

            <div class="section-content" id="mi-cuenta-section" style="display: none;">
                <div class="card border-0 shadow-sm p-4">
                    <h3>Mi Perfil</h3>
                    <hr>
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <i class="bi bi-person-circle" style="font-size: 5rem; color: #dee2e6;"></i>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="text-muted small d-block">Nombre completo</label>
                                <span class="h5 text-capitalize">{{ session('usuario.nombre') }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small d-block">Correo de contacto</label>
                                <span class="h5">{{ session('usuario.email') }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small d-block">Tipo de usuario</label>
                                <span class="badge bg-secondary">CLIENTE REGISTRADO</span>
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

        function showSection(sectionId) {
            sections.forEach(s => s.style.display = 'none');
            const target = document.getElementById(sectionId + '-section');
            if (target) {
                target.style.display = 'block';
                // Animación simple de entrada
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
                
                // Cerrar sidebar en dispositivos móviles tras click
                if(window.innerWidth < 768) {
                    document.querySelector('.sidebar').classList.remove('show');
                    document.getElementById('sidebarOverlay').classList.remove('show');
                }
            });
        });

        // Lógica de apertura/cierre de Sidebar
        const toggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const close = document.getElementById('sidebarClose');

        if(toggle) toggle.addEventListener('click', () => { sidebar.classList.add('show'); overlay.classList.add('show'); });
        if(overlay) overlay.addEventListener('click', () => { sidebar.classList.remove('show'); overlay.classList.remove('show'); });
        if(close) close.addEventListener('click', () => { sidebar.classList.remove('show'); overlay.classList.remove('show'); });
    });
</script>
@endpush