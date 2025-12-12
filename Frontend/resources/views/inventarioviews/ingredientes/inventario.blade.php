{{-- resources/views/inventarioviews/ingredientes/inventario.blade.php --}}

@extends('layouts.app')

@section('title', 'Inventario de Ingredientes')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="{{ asset('css/variables.css') }}" rel="stylesheet">
<link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    .inventory-card {
        transition: all 0.3s ease;
        border-left: 4px solid var(--panaderia-marron-principal);
    }
    .inventory-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1) !important;
    }
    .stock-badge {
        font-size: 1.5rem;
        font-weight: 600;
    }
    .low-stock {
        border-left-color: #dc3545 !important;
    }
    .medium-stock {
        border-left-color: #ffc107 !important;
    }
    .good-stock {
        border-left-color: #28a745 !important;
    }
    .ingredient-icon {
        font-size: 2rem;
        opacity: 0.7;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        
        {{-- Sidebar --}}
        @include('components.admin-sidebar')
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
            
            {{-- Header --}}
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-boxes-stacked me-2" style="color: var(--panaderia-marron-principal);"></i>
                    Inventario de Ingredientes
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('ingredientes.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-list me-1"></i> Vista Completa
                    </a>
                    <a href="{{ route('ingredientes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Agregar Ingrediente
                    </a>
                </div>
            </div>

            {{-- Mensajes de alerta --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Estadísticas Rápidas --}}
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Total Ingredientes</p>
                                    <h3 class="mb-0">{{ count($ingredientes) }}</h3>
                                </div>
                                <i class="fas fa-cubes fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Stock Bajo</p>
                                    <h3 class="mb-0 text-danger">
                                        {{ collect($ingredientes)->filter(fn($i) => $i['cantidadIngrediente'] < 10)->count() }}
                                    </h3>
                                </div>
                                <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Stock Disponible</p>
                                    <h3 class="mb-0 text-success">
                                        {{ collect($ingredientes)->filter(fn($i) => $i['cantidadIngrediente'] >= 10)->count() }}
                                    </h3>
                                </div>
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Buscador --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control" placeholder="Buscar ingrediente...">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary active" onclick="filterStock('all')">
                            <i class="fas fa-filter me-1"></i> Todos
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="filterStock('low')">
                            Stock Bajo
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="filterStock('good')">
                            Stock Bueno
                        </button>
                    </div>
                </div>
            </div>

            {{-- Lista de Ingredientes en Cards --}}
            @if (empty($ingredientes))
                <div class="alert alert-info text-center py-5">
                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                    <h4>No hay ingredientes registrados</h4>
                    <p>Comienza agregando tu primer ingrediente al inventario.</p>
                    <a href="{{ route('ingredientes.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-2"></i>Agregar Ingrediente
                    </a>
                </div>
            @else
                <div class="row g-3" id="ingredientesContainer">
                    @foreach ($ingredientes as $ingrediente)
                        @php
                            $cantidad = $ingrediente['cantidadIngrediente'] ?? 0;
                            $stockClass = $cantidad < 10 ? 'low-stock' : ($cantidad < 50 ? 'medium-stock' : 'good-stock');
                            $stockBadgeClass = $cantidad < 10 ? 'bg-danger' : ($cantidad < 50 ? 'bg-warning' : 'bg-success');
                        @endphp
                        
                        <div class="col-md-6 col-lg-4 col-xl-3 ingredient-item" data-stock="{{ $cantidad }}" data-name="{{ strtolower($ingrediente['nombreIngrediente']) }}">
                            <div class="card inventory-card border-0 shadow-sm h-100 {{ $stockClass }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <i class="fas fa-pepper-hot ingredient-icon" style="color: var(--panaderia-marron-principal);"></i>
                                        <span class="badge {{ $stockBadgeClass }} stock-badge">{{ number_format($cantidad, 1) }}</span>
                                    </div>
                                    
                                    <h5 class="card-title mb-2">{{ $ingrediente['nombreIngrediente'] }}</h5>
                                    <p class="text-muted small mb-3">ID: {{ $ingrediente['idIngrediente'] }}</p>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">Unidades disponibles</small>
                                        <a href="{{ route('ingredientes.index') }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </main>
    </div>
</div>

@push('scripts')
<script>
    // Búsqueda en tiempo real
    document.getElementById('searchInput').addEventListener('keyup', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const items = document.querySelectorAll('.ingredient-item');
        
        items.forEach(item => {
            const name = item.getAttribute('data-name');
            if (name.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Filtro por stock
    function filterStock(type) {
        const items = document.querySelectorAll('.ingredient-item');
        const buttons = document.querySelectorAll('.btn-group button');
        
        // Actualizar botones activos
        buttons.forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        
        items.forEach(item => {
            const stock = parseFloat(item.getAttribute('data-stock'));
            
            if (type === 'all') {
                item.style.display = '';
            } else if (type === 'low' && stock < 10) {
                item.style.display = '';
            } else if (type === 'good' && stock >= 10) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }
</script>
@endpush

@endsection