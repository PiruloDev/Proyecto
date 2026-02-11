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
        font-size: 1.3rem;
        font-weight: 600;
        padding: 0.5em 0.8em;
    }
    .low-stock { border-left-color: #dc3545 !important; }
    .medium-stock { border-left-color: #ffc107 !important; }
    .good-stock { border-left-color: #28a745 !important; }

    /* Mejora de scroll para stats en móviles */
    @media (max-width: 768px) {
        .stats-scroll {
            display: flex;
            overflow-x: auto;
            padding-bottom: 15px;
            gap: 15px;
            -webkit-overflow-scrolling: touch;
        }
        .stats-scroll .col-md-4 {
            flex: 0 0 85%;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        
        @include('components.admin-sidebar')
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pb-5">
            
            {{-- Header Responsivo --}}
            <div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2 mb-2 mb-md-0">
                    <i class="fas fa-boxes-stacked me-2" style="color: var(--panaderia-marron-principal);"></i>
                    Inventario
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('ingredientes.index') }}" class="btn btn-sm btn-outline-secondary shadow-sm">
                        <i class="fas fa-list me-1"></i> <span class="d-none d-sm-inline">Vista Completa</span>
                    </a>
                </div>
            </div>

            {{-- Contenedor de Alertas --}}
            <div id="alertPlaceholder">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>

            {{-- Estadísticas Rápidas (Scrollable en mobile) --}}
            <div class="row g-3 mb-4 stats-scroll">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1 small text-uppercase fw-bold">Total Ingredientes</p>
                                    <h3 class="mb-0">{{ count($ingredientes) }}</h3>
                                </div>
                                <i class="fas fa-cubes fa-2x text-primary opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1 small text-uppercase fw-bold">Stock Bajo</p>
                                    <h3 class="mb-0 text-danger">
                                        {{ collect($ingredientes)->filter(fn($i) => $i['cantidadIngrediente'] < 10)->count() }}
                                    </h3>
                                </div>
                                <i class="fas fa-exclamation-triangle fa-2x text-danger opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1 small text-uppercase fw-bold">Stock Disponible</p>
                                    <h3 class="mb-0 text-success">
                                        {{ collect($ingredientes)->filter(fn($i) => $i['cantidadIngrediente'] >= 10)->count() }}
                                    </h3>
                                </div>
                                <i class="fas fa-check-circle fa-2x text-success opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Buscador y Filtros Adaptativos --}}
            <div class="row g-3 mb-4">
                <div class="col-12 col-lg-6">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Buscar ingrediente...">
                    </div>
                </div>
                <div class="col-12 col-lg-6 text-lg-end">
                    <div class="btn-group w-100 w-lg-auto shadow-sm" role="group">
                        <button type="button" class="btn btn-outline-secondary active btn-filter" onclick="filterStock('all', this)">Todos</button>
                        <button type="button" class="btn btn-outline-danger btn-filter" onclick="filterStock('low', this)">Bajo</button>
                        <button type="button" class="btn btn-outline-success btn-filter" onclick="filterStock('good', this)">Óptimo</button>
                    </div>
                </div>
            </div>

            {{-- Grid de Cards --}}
            <div class="row g-3" id="ingredientesContainer">
                @forelse ($ingredientes as $ingrediente)
                    @php
                        $cantidad = $ingrediente['cantidadIngrediente'] ?? 0;
                        $stockClass = $cantidad < 10 ? 'low-stock' : ($cantidad < 50 ? 'medium-stock' : 'good-stock');
                        $stockBadgeClass = $cantidad < 10 ? 'bg-danger' : ($cantidad < 50 ? 'bg-warning' : 'bg-success');
                    @endphp
                    
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3 ingredient-item" data-stock="{{ $cantidad }}" data-name="{{ strtolower($ingrediente['nombreIngrediente']) }}">
                        <div class="card inventory-card border-0 shadow-sm h-100 {{ $stockClass }}">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title fw-bold mb-0 text-dark">{{ $ingrediente['nombreIngrediente'] }}</h5>
                                    <span class="badge {{ $stockBadgeClass }} stock-badge shadow-sm">{{ number_format($cantidad, 1) }}</span>
                                </div>
                                
                                <p class="text-muted small mb-4">Código: #{{ $ingrediente['idIngrediente'] }}</p>
                                
                                <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                    <button type="button" class="btn btn-success btn-sm btn-ingreso-stock px-3" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#ingresoStockModal"
                                            data-id="{{ $ingrediente['idIngrediente'] }}" 
                                            data-name="{{ $ingrediente['nombreIngrediente'] }}">
                                        <i class="fas fa-plus-circle me-1"></i> Reponer
                                    </button>
                                    <a href="{{ route('ingredientes.index') }}" class="btn btn-sm btn-light border text-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">No se encontraron ingredientes.</p>
                    </div>
                @endforelse
            </div>
        </main>
    </div>
</div>

{{-- El modal y los scripts se mantienen igual para asegurar funcionamiento AJAX --}}
<div class="modal fade" id="ingresoStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-truck-ramp-box me-2"></i> Reponer Stock</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formIngresoStock">
                <div class="modal-body">
                    <input type="hidden" id="ingredienteId">
                    <p class="mb-4">Ingresando stock para: <strong id="ingredienteNombreModal"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Cantidad a Ingresar</label>
                        <input type="number" step="0.01" min="0.01" class="form-control form-control-lg" id="cantidadIngresada" required>
                        <div class="invalid-feedback" id="cantidadIngresadaFeedback">Ingrese una cantidad válida.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="btnGuardarStock">Registrar Ingreso</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Búsqueda en tiempo real
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        document.querySelectorAll('.ingredient-item').forEach(item => {
            const name = item.getAttribute('data-name');
            item.style.display = name.includes(searchTerm) ? '' : 'none';
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

    // Lógica AJAX para actualización de stock
    const modal = document.getElementById('ingresoStockModal');
    modal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        document.getElementById('ingredienteId').value = btn.getAttribute('data-id');
        document.getElementById('ingredienteNombreModal').textContent = btn.getAttribute('data-name');
        document.getElementById('cantidadIngresada').value = '';
    });

    document.getElementById('formIngresoStock').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('ingredienteId').value;
        const cantidad = document.getElementById('cantidadIngresada').value;
        const btnSave = document.getElementById('btnGuardarStock');

        btnSave.disabled = true;
        btnSave.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

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
            btnSave.textContent = 'Registrar Ingreso';
            alert(error.message);
        });
    });
</script>
@endpush
@endsection