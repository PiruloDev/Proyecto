{{-- resources/views/inventarioviews/ingredientes/index.blade.php --}}

@extends('layouts.app') 

@section('title', 'Gestión de Ingredientes - El Castillo del Pan')
    
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* RESET PARA SCROLL INDEPENDIENTE */
        html, body {
            height: 100%;
            margin: 0;
            overflow: hidden; /* Evita el scroll doble en la página */
        }

        .container-fluid, .row {
            height: 100%;
        }

        /* CONFIGURACIÓN DEL ÁREA PRINCIPAL */
        main.col-md-9 {
            height: 100vh;
            overflow-y: auto; /* Scroll solo en el contenido */
            display: flex;
            flex-direction: column;
            background-color: #fcfcfc;
        }

        /* HEADER PERSISTENTE (Sticky) */
        .sticky-header {
            position: sticky;
            top: 0;
            background-color: white;
            z-index: 1000;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        /* ESTILOS DE CARDS */
        .admin-card {
            transition: transform 0.2s, box-shadow 0.2s;
            border: 1px solid #eee;
            border-top: 3px solid var(--panaderia-marron-principal);
        }
        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }

        /* LIMITACIÓN DE MENÚS DESPLEGABLES (SCROLL INTERNO) */
        .form-select {
            max-height: 200px;
            cursor: pointer;
        }

        /* UI ELEMENTS */
        .id-badge {
            background-color: #f8f9fa;
            color: #6c757d;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: bold;
        }

        .meta-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            color: #999;
            display: block;
        }

        .meta-value {
            font-weight: 600;
            color: #444;
        }

        /* DISEÑO DEL SCROLLBAR */
        main::-webkit-scrollbar {
            width: 6px;
        }
        main::-webkit-scrollbar-thumb {
            background: #d1d1d1;
            border-radius: 10px;
        }
        main::-webkit-scrollbar-thumb:hover {
            background: var(--panaderia-marron-principal);
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        
        {{-- Sidebar fijo --}}
        @include('components.admin-sidebar') 
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pb-5">
            
            {{-- Header Superior y Buscador (Se mantienen arriba al scrolear) --}}
            <div class="sticky-header">
                <div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-3">
                    <div>
                        <h1 class="h2 fw-bold mb-0">Catálogo de Ingredientes</h1>
                        <p class="text-muted small mb-0">Administración de base de datos y referencias</p>
                    </div>
                    <button class="btn btn-primary shadow-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#crearModal">
                        <i class="fas fa-plus me-2"></i>Nuevo Ingrediente
                    </button>
                </div>

                {{-- Buscador Dinámico --}}
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="search-wrapper position-relative">
                            <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            <input type="text" id="cardSearch" class="form-control border-0 shadow-sm ps-5" placeholder="Buscar por nombre o ID..." style="background-color: #f8f9fa;">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Alertas --}}
            <div class="mt-3">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>
                    
            {{-- Grid de Cards (Zona de Scroll) --}}
            <div class="row g-3 mt-2" id="adminCardsContainer">
                @forelse($ingredientes as $ing)
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3 card-item" data-string="{{ strtolower($ing['nombreIngrediente']) }} {{ $ing['idIngrediente'] }}">
                        <div class="card admin-card h-100 shadow-sm border-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="id-badge">ID: #{{ $ing['idIngrediente'] }}</span>
                                    <div class="dropdown">
                                        <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editModal-{{ $ing['idIngrediente'] }}">
                                                <i class="fas fa-edit me-2 text-warning"></i>Editar</a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form method="POST" action="{{ route('ingredientes.destroy', $ing['idIngrediente']) }}" onsubmit="return confirm('¿Eliminar este ingrediente?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash me-2"></i>Eliminar
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <h5 class="card-title fw-bold text-dark mb-3">{{ $ing['nombreIngrediente'] }}</h5>
                                
                                <div class="row g-2">
                                    <div class="col-6">
                                        <span class="meta-label">Proveedor</span>
                                        <span class="meta-value text-truncate d-block">
                                            <i class="fas fa-truck me-1 small"></i>
                                            {{ $ing['nombreProv'] ?? 'ID: '.$ing['idProveedor'] }}
                                        </span>
                                    </div>
                                    <div class="col-6">
                                        <span class="meta-label">Categoría</span>
                                        <span class="meta-value text-truncate d-block">
                                            <i class="fas fa-tag me-1 small"></i>
                                            {{ $ing['nombreCategoria'] ?? 'ID: '.$ing['idCategoria'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                                <button class="btn btn-outline-warning btn-sm w-100 rounded-pill" data-bs-toggle="modal" data-bs-target="#editModal-{{ $ing['idIngrediente'] }}">
                                    <i class="fas fa-pencil-alt me-1"></i> Gestionar Datos
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- MODAL EDITAR --}}
                    <div class="modal fade" id="editModal-{{ $ing['idIngrediente'] }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-warning text-dark">
                                    <h5 class="modal-title fw-bold">Editar Ingrediente</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" action="{{ route('ingredientes.update', $ing['idIngrediente']) }}">
                                    @csrf @method('PUT')
                                    <div class="modal-body p-4">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label fw-bold">Nombre</label>
                                                <input type="text" name="nombreIngrediente" class="form-control" value="{{ $ing['nombreIngrediente'] }}" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-bold">Referencia</label>
                                                <input type="text" name="referenciaIngrediente" class="form-control" value="{{ $ing['referenciaIngrediente'] }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Proveedor</label>
                                                <select name="idProveedor" class="form-select" required>
                                                    @foreach($proveedores as $prov)
                                                        <option value="{{ $prov['idProveedor'] }}" {{ $ing['idProveedor'] == $prov['idProveedor'] ? 'selected' : '' }}>
                                                            {{ $prov['nombreProv'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Categoría</label>
                                                <select name="idCategoria" class="form-select" required>
                                                    @foreach($categorias as $cat)
                                                        <option value="{{ $cat['idCategoria'] }}" {{ $ing['idCategoria'] == $cat['idCategoria'] ? 'selected' : '' }}>
                                                            {{ $cat['nombreCategoria'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-warning rounded-pill px-4">Guardar Cambios</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No se encontraron ingredientes registrados.</p>
                    </div>
                @endforelse
            </div>
        </main>
    </div>
</div>

{{-- MODAL CREAR --}}
<div class="modal fade" id="crearModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Nuevo Ingrediente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('ingredientes.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Nombre del Ingrediente</label>
                            <input type="text" name="nombreIngrediente" class="form-control" placeholder="Ej: Harina de Trigo" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Referencia / Marca</label>
                            <input type="text" name="referenciaIngrediente" class="form-control" placeholder="Ej: Marca Patito 1kg" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Proveedor</label>
                            <select name="idProveedor" class="form-select" required>
                                <option value="" disabled selected>Elegir...</option>
                                @foreach($proveedores as $prov)
                                    <option value="{{ $prov['idProveedor'] }}">{{ $prov['nombreProv'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Categoría</label>
                            <select name="idCategoria" class="form-select" required>
                                <option value="" disabled selected>Elegir...</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat['idCategoria'] }}">{{ $cat['nombreCategoria'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPTS --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('cardSearch');
        const cards = document.querySelectorAll('.card-item');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();

            cards.forEach(card => {
                const text = card.getAttribute('data-string').toLowerCase();
                card.style.display = text.includes(searchTerm) ? 'block' : 'none';
            });
        });
    });
</script>
@endpush

@endsection