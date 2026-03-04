{{-- resources/views/inventarioviews/ingredientes/inventario.blade.php --}}

@extends('layouts.app')

@section('title', 'Inventario de Ingredientes - El Castillo del Pan')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="{{ asset('css/variables.css') }}" rel="stylesheet">
<link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    /* Contenedor Principal con Scroll Interno */
    .main-content {
        height: 100vh;
        overflow-y: auto;
        background-color: #fdfbf9;
        padding-bottom: 50px;
    }

    /* Cards de Inventario */
    .inventory-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 20px !important;
        background: white;
        border-left: 6px solid #d7ccc8; /* Color por defecto */
    }
    .inventory-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(93, 64, 55, 0.1) !important;
    }

    /* Estados de Stock */
    .low-stock { border-left-color: #dc3545 !important; }    /* Rojo */
    .medium-stock { border-left-color: #ffc107 !important; } /* Amarillo */
    .good-stock { border-left-color: #28a745 !important; }   /* Verde */

    .stock-badge {
        font-size: 1.1rem;
        font-weight: 700;
        padding: 8px 15px;
        border-radius: 12px;
    }

    /* Buscador Estilizado */
    .search-wrapper {
        position: relative;
        max-width: 450px;
    }
    .search-wrapper i {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #8d6e63;
    }
    .search-input {
        padding-left: 50px !important;
        border-radius: 50px !important;
        height: 48px;
        border: 1px solid #d7ccc8 !important;
    }

    /* Botones y Filtros */
    .btn-marca {
        background-color: #5d4037 !important;
        color: white !important;
        border: none;
        border-radius: 10px;
    }
    .btn-filter.active {
        background-color: #5d4037 !important;
        color: white !important;
        border-color: #5d4037 !important;
    }

    /* Scrollbar */
    .main-content::-webkit-scrollbar { width: 6px; }
    .main-content::-webkit-scrollbar-thumb { background: #d7ccc8; border-radius: 10px; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        
        @include('components.admin-sidebar')
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4 main-content">
            
            {{-- HEADER --}}
<div class="d-flex justify-content-between align-items-center pt-4 pb-2 mb-4 border-bottom">
    <div class="d-flex align-items-center">
        {{-- Botón Volver --}}
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary border-0 me-3" style="color: #5d4037; font-size: 1.5rem; transition: transform 0.2s;">
            <i class="fas fa-arrow-left"></i>
        </a>

        <div>
            <h1 class="h2 fw-bold mb-0" style="color: #3e2723;">
                <i class="fas fa-boxes-stacked me-2"></i>Control de Inventario
            </h1>
            <p class="text-muted small mb-0">Supervisa y repone stock de ingredientes en tiempo real.</p>
        </div>
    </div>

    <div class="btn-group shadow-sm">
        <a href="{{ route('ingredientes.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-list me-1"></i> Lista Completa
        </a>
    </div>
</div>

            {{-- ALERTAS --}}
            @if (session('success') || request('success'))
                <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') ?? request('success') }}
                </div>
            @endif

            {{-- ESTADÍSTICAS RÁPIDAS --}}
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3" style="border-radius: 15px;">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-light p-3 me-3 text-brown">
                                <i class="fas fa-database fa-lg"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small fw-bold text-uppercase">Total Items</p>
                                <h4 class="fw-bold mb-0">{{ count($ingredientes) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3" style="border-radius: 15px;">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-danger-soft p-3 me-3 text-danger">
                                <i class="fas fa-arrow-down fa-lg"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small fw-bold text-uppercase">Stock Bajo</p>
                                <h4 class="fw-bold mb-0 text-danger">
                                    {{ collect($ingredientes)->filter(fn($i) => ($i['cantidadIngrediente'] ?? 0) < 10)->count() }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3" style="border-radius: 15px;">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-success-soft p-3 me-3 text-success">
                                <i class="fas fa-check-double fa-lg"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small fw-bold text-uppercase">Stock Óptimo</p>
                                <h4 class="fw-bold mb-0 text-success">
                                    {{ collect($ingredientes)->filter(fn($i) => ($i['cantidadIngrediente'] ?? 0) >= 10)->count() }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BUSCADOR Y FILTROS --}}
            <div class="row g-3 mb-4 align-items-center">
                <div class="col-12 col-lg-6">
                    <div class="search-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" class="form-control search-input shadow-sm" placeholder="Buscar por nombre o código...">
                    </div>
                </div>
                <div class="col-12 col-lg-6 text-lg-end">
                    <div class="btn-group shadow-sm bg-white p-1" style="border-radius: 12px; border: 1px solid #d7ccc8;">
                        <button class="btn btn-filter active px-4" onclick="filterStock('all', this)">Todos</button>
                        <button class="btn btn-filter text-danger px-4" onclick="filterStock('low', this)">Bajo</button>
                        <button class="btn btn-filter text-success px-4" onclick="filterStock('good', this)">Óptimo</button>
                    </div>
                </div>
            </div>

            {{-- GRID DE CARDS --}}
            <div class="row g-4" id="ingredientesContainer">
                @forelse ($ingredientes as $ingrediente)
                    @php
                        $cantidad = $ingrediente['cantidadIngrediente'] ?? 0;
                        $stockClass = $cantidad < 10 ? 'low-stock' : ($cantidad < 50 ? 'medium-stock' : 'good-stock');
                        $badgeClass = $cantidad < 10 ? 'bg-danger' : ($cantidad < 50 ? 'bg-warning text-dark' : 'bg-success');
                    @endphp
                    
                    <div class="col-12 col-sm-6 col-xl-3 ingredient-item" data-stock="{{ $cantidad }}" data-name="{{ strtolower($ingrediente['nombreIngrediente']) }}">
                        <div class="card inventory-card shadow-sm h-100 {{ $stockClass }}">
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="fw-bold mb-0 text-brown" style="color: #5d4037;">{{ $ingrediente['nombreIngrediente'] }}</h5>
                                    <span class="badge {{ $badgeClass }} stock-badge shadow-sm">{{ number_format($cantidad, 1) }}</span>
                                </div>
                                
                                <p class="text-muted small mb-4">
                                    <i class="fas fa-barcode me-1"></i> ID: #{{ $ingrediente['idIngrediente'] }}
                                </p>
                                
                                <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                    <button type="button" class="btn btn-success btn-sm px-3 rounded-pill" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#ingresoStockModal"
                                            data-id="{{ $ingrediente['idIngrediente'] }}" 
                                            data-name="{{ $ingrediente['nombreIngrediente'] }}">
                                        <i class="fas fa-plus-circle me-1"></i> Reponer
                                    </button>
                                    <a href="{{ route('ingredientes.index') }}" class="btn btn-sm btn-light rounded-circle" title="Ver detalles">
                                        <i class="fas fa-eye text-brown"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted opacity-25 mb-3"></i>
                        <p class="text-muted">No se encontraron ingredientes en el inventario.</p>
                    </div>
                @endforelse
            </div>
        </main>
    </div>
</div>

{{-- MODAL REPOSICIÓN --}}
<div class="modal fade" id="ingresoStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-success text-white border-0 p-4" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fas fa-truck-loading me-2"></i> Reponer Inventario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formIngresoStock">
                <div class="modal-body p-4">
                    <input type="hidden" id="ingredienteId">
                    <div class="text-center mb-4">
                        <p class="text-muted mb-1">Ingrediente seleccionado:</p>
                        <h4 class="fw-bold text-brown" id="ingredienteNombreModal"></h4>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Cantidad a Ingresar</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0.01" class="form-control form-control-lg text-center" id="cantidadIngresada" placeholder="0.00" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-light" style="border-radius: 0 0 20px 20px;">
                    <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success px-4 shadow" id="btnGuardarStock">Confirmar Ingreso</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Buscador en tiempo real mejorado
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        document.querySelectorAll('.ingredient-item').forEach(item => {
            const name = item.getAttribute('data-name');
            const code = item.textContent.toLowerCase();
            item.style.display = (name.includes(searchTerm) || code.includes(searchTerm)) ? '' : 'none';
        });
    });

    // Filtro por stock
    function filterStock(type, btn) {
        document.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        document.querySelectorAll('.ingredient-item').forEach(item => {
            const stock = parseFloat(item.getAttribute('data-stock'));
            if (type === 'all') item.style.display = '';
            else if (type === 'low') item.style.display = stock < 10 ? '' : 'none';
            else if (type === 'good') item.style.display = stock >= 10 ? '' : 'none';
        });
    }

    // Modal data binding
    const modal = document.getElementById('ingresoStockModal');
    modal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        document.getElementById('ingredienteId').value = btn.getAttribute('data-id');
        document.getElementById('ingredienteNombreModal').textContent = btn.getAttribute('data-name');
        document.getElementById('cantidadIngresada').value = '';
    });

    // Fetch AJAX para stock
    document.getElementById('formIngresoStock').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('ingredienteId').value;
        const cantidad = document.getElementById('cantidadIngresada').value;
        const btnSave = document.getElementById('btnGuardarStock');

        btnSave.disabled = true;
        btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';

        fetch(`{{ url('/ingredientes') }}/${id}/ingresar-stock`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ cantidadIngresada: cantidad })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) throw new Error(data.error);
            window.location.href = `{{ url()->current() }}?success=${encodeURIComponent(data.message)}`;
        })
        .catch(error => {
            btnSave.disabled = false;
            btnSave.textContent = 'Confirmar Ingreso';
            alert('Error: ' + error.message);
        });
    });
</script>
@endpush
@endsection