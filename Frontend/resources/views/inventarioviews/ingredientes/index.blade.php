{{-- resources/views/inventarioviews/ingredientes/index.blade.php --}}

@extends('layouts.app') 

@section('title', 'Gestión de Ingredientes - El Castillo del Pan')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* Contenedor principal con scroll interno */
        .main-content {
            height: 100vh;
            overflow-y: auto;
            padding-bottom: 3rem;
            background: linear-gradient(135deg, var(--panaderia-beige-claro) 0%, var(--panaderia-blanco-calido) 100%);
        }

        /* Diseño de las Tarjetas de Ingredientes */
        .ingrediente-card {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            border: 1px solid rgba(187, 148, 103, 0.2) !important;
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
            border-radius: 20px !important;
        }

        .ingrediente-card:hover {
            transform: translateY(-8px);
            background: rgba(255, 255, 255, 0.9);
            box-shadow: var(--panaderia-shadow-xl) !important;
            border-color: var(--panaderia-marron-principal) !important;
        }

        .search-container {
            position: relative;
        }

        .search-container i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--panaderia-marron-principal);
        }

        .search-input {
            padding-left: 50px !important;
            border-radius: 50px !important;
            height: 50px;
            border: 1px solid var(--panaderia-beige-oscuro) !important;
            box-shadow: var(--panaderia-shadow-sm);
        }

        /* Estilo para los Modales unificados */
        .modal-content {
            border-radius: 25px;
            border: none;
        }

        .modal-header {
            border-radius: 25px 25px 0 0;
            padding: 1.5rem;
        }

        .btn-panaderia {
            background: var(--panaderia-marron-principal);
            color: white;
            border-radius: 12px;
            transition: all 0.3s;
        }

        .btn-panaderia:hover {
            background: var(--panaderia-marron-hover);
            color: white;
            transform: scale(1.02);
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        
        {{-- Sidebar --}}
        @include('components.admin-sidebar') 
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4 main-content">
            
            {{-- Header --}}
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-2 mb-4">
                <div>
                    <h1 class="h2 fw-bold" style="color: var(--panaderia-marron-oscuro);">Catálogo de Ingredientes</h1>
                    <p class="text-muted">Gestión visual de insumos para producción.</p>
                </div>
                <button class="btn btn-panaderia px-4 py-2" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fas fa-plus-circle me-2"></i> Agregar Ingrediente
                </button>
            </div>

            {{-- Buscador --}}
            <div class="row mb-5">
                <div class="col-md-6 col-lg-5">
                    <div class="search-container">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" class="form-control search-input" placeholder="Buscar por nombre, referencia o ID...">
                    </div>
                </div>
            </div>

            {{-- Alertas --}}
            @if (session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">{{ session('success') }}</div>
            @endif

            {{-- Grid de Contenido --}}
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4" id="ingredientesGrid">
                @forelse($ingredientes as $ing)
                <div class="col ingrediente-item">
                    <div class="card ingrediente-card h-100 shadow-sm border-0">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge rounded-pill px-3" style="background: rgba(166, 124, 82, 0.1); color: var(--panaderia-marron-principal);">
                                    ID #{{ $ing['idIngrediente'] }}
                                </span>
                                <i class="bi bi-box-seam fs-4 text-muted opacity-50"></i>
                            </div>

                            <h5 class="fw-bold mb-1" style="color: var(--panaderia-gris-oscuro);">{{ $ing['nombreIngrediente'] }}</h5>
                            <code class="mb-3 d-block text-secondary">{{ $ing['referenciaIngrediente'] }}</code>

                            <div class="bg-white rounded-3 p-3 mb-4 shadow-sm" style="border: 1px solid rgba(0,0,0,0.03);">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-truck me-2 text-primary"></i>
                                    <small class="text-muted">Proveedor ID: <strong>{{ $ing['idProveedor'] }}</strong></small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-tag me-2 text-success"></i>
                                    <small class="text-muted">Categoría ID: <strong>{{ $ing['idCategoria'] }}</strong></small>
                                </div>
                            </div>

                            {{-- Acciones --}}
                            <div class="d-flex gap-2 mt-auto">
                                <button class="btn btn-outline-warning btn-sm flex-grow-1 border-2 fw-bold" data-bs-toggle="modal" data-bs-target="#editModal-{{ $ing['idIngrediente'] }}">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <form method="POST" action="{{ route('ingredientes.destroy', $ing['idIngrediente']) }}" class="flex-grow-1">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100 border-2 fw-bold" onclick="return confirm('¿Eliminar este ingrediente?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MODAL EDITAR (Integrado en el bucle) --}}
                <div class="modal fade" id="editModal-{{ $ing['idIngrediente'] }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content shadow-lg">
                            <div class="modal-header bg-warning text-dark">
                                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Editar #{{ $ing['idIngrediente'] }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="{{ route('ingredientes.update', $ing['idIngrediente']) }}">
                                @csrf @method('PUT')
                                <div class="modal-body p-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nombre del Ingrediente</label>
                                        <input type="text" name="nombreIngrediente" class="form-control shadow-sm" value="{{ $ing['nombreIngrediente'] }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Referencia</label>
                                        <input type="text" name="referenciaIngrediente" class="form-control shadow-sm" value="{{ $ing['referenciaIngrediente'] }}" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">ID Proveedor</label>
                                            <input type="number" name="idProveedor" class="form-control shadow-sm" value="{{ $ing['idProveedor'] }}" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">ID Categoría</label>
                                            <input type="number" name="idCategoria" class="form-control shadow-sm" value="{{ $ing['idCategoria'] }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-warning px-4 rounded-3 fw-bold">Guardar Cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5 glass-card rounded-4">
                        <i class="bi bi-search fs-1 text-muted"></i>
                        <h4 class="mt-3 text-muted">No se encontraron ingredientes</h4>
                    </div>
                </div>
                @endforelse
            </div>
        </main>
    </div>
</div>

{{-- MODAL CREAR (Fuera del bucle) --}}
<div class="modal fade" id="crearModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header text-white" style="background: var(--panaderia-marron-oscuro);">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Nuevo Ingrediente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('ingredientes.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Ingrediente</label>
                        <input type="text" name="nombreIngrediente" class="form-control shadow-sm" placeholder="Ej. Harina de Trigo" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Referencia</label>
                        <input type="text" name="referenciaIngrediente" class="form-control shadow-sm" placeholder="Ej. SKU-HT-01" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">ID Proveedor</label>
                            <input type="number" name="idProveedor" class="form-control shadow-sm" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">ID Categoría</label>
                            <input type="number" name="idCategoria" class="form-control shadow-sm" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-panaderia px-4 fw-bold">Registrar Ingrediente</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Buscador instantáneo para el grid
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let value = this.value.toLowerCase();
        let items = document.querySelectorAll('.ingrediente-item');
        
        items.forEach(item => {
            let text = item.innerText.toLowerCase();
            item.style.display = text.includes(value) ? '' : 'none';
        });
    });
</script>
@endpush