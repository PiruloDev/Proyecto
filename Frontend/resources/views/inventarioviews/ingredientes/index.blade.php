@extends('layouts.app') 

@section('title', 'Gestión de Ingredientes - El Castillo del Pan')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        .main-content {
            height: 100vh;
            overflow-y: auto;
            padding-bottom: 3rem;
            background: linear-gradient(135deg, var(--panaderia-beige-claro) 0%, var(--panaderia-blanco-calido) 100%);
        }

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
        }

        .badge-unidad {
            background: rgba(166, 124, 82, 0.1);
            color: var(--panaderia-marron-principal);
            font-size: 0.75rem;
            font-weight: 700;
            border: 1px solid rgba(166, 124, 82, 0.3);
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('components.admin-sidebar') 
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4 main-content">

            {{-- Header --}}
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-2 mb-4">
                <div>
                    {{-- Alertas --}}
                    @if ($errors->any())
                        <div class="alert alert-danger shadow-sm border-0 mb-4" style="border-radius: 15px;">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success shadow-sm border-0 mb-4" style="border-radius: 15px;">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger shadow-sm border-0 mb-4" style="border-radius: 15px;">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                        </div>
                    @endif
                    <a href="{{ route('dashboard.inventario') }}" class="btn btn-outline-secondary border-0 me-3" style="color: #5d4037; font-size: 1.5rem; transition: transform 0.2s;">
                                <i class="fas fa-arrow-left"></i>
                    </a>
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
                    <div class="search-container position-relative">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" class="form-control search-input"
                               placeholder="Buscar por nombre, proveedor o referencia...">
                    </div>
                </div>
            </div>

            {{-- Grid de Ingredientes --}}
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4" id="ingredientesGrid">
                @forelse($ingredientes as $ing)
               @php
                    $nombreProv = collect($proveedores)->firstWhere('idProveedor', $ing['idProveedor'])['nombreProv'] ?? 'No asignado';
                    $nombreCat  = collect($categorias)->firstWhere('idCategoriaIngrediente', $ing['idCategoria'])['nombreCategoria'] ?? 'General'; // ← cambiar idCategoria por idCategoriaIngrediente
                @endphp
                <div class="col ingrediente-item">
                    <div class="card ingrediente-card h-100 shadow-sm border-0">
                        <div class="card-body p-4 d-flex flex-column">

                            {{-- Cabecera de la card --}}
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge rounded-pill px-3"
                                      style="background: rgba(166,124,82,0.1); color: var(--panaderia-marron-principal);">
                                    ID #{{ $ing['idIngrediente'] }}
                                </span>
                                <i class="bi bi-box-seam fs-4 text-muted opacity-50"></i>
                            </div>

                            {{-- Nombre y referencia --}}
                            <h5 class="fw-bold mb-1 text-dark">{{ $ing['nombreIngrediente'] }}</h5>
                            <code class="mb-2 d-block text-secondary">{{ $ing['referenciaIngrediente'] }}</code>

                            {{-- Abreviatura de unidad ← NUEVO --}}
                            @if(!empty($ing['abreviaturaUnidad']))
                                <span class="badge badge-unidad rounded-pill px-3 mb-3 w-fit">
                                    <i class="bi bi-rulers me-1"></i>{{ $ing['abreviaturaUnidad'] }}
                                </span>
                            @endif

                            {{-- Info proveedor y categoría --}}
                            <div class="bg-white rounded-3 p-3 mb-4 shadow-sm">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-truck me-2 text-primary"></i>
                                    <small class="text-muted">Prov: <strong class="text-dark">{{ $nombreProv }}</strong></small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-tag me-2 text-success"></i>
                                    <small class="text-muted">Cat: <strong class="text-dark">{{ $nombreCat }}</strong></small>
                                </div>
                            </div>

                            {{-- Botones --}}
                            <div class="d-flex gap-2 mt-auto">
                                <button class="btn btn-outline-warning btn-sm flex-grow-1 border-2 fw-bold"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal-{{ $ing['idIngrediente'] }}">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <form method="POST"
                                      action="{{ route('ingredientes.destroy', $ing['idIngrediente']) }}"
                                      class="flex-grow-1">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-outline-danger btn-sm w-100 border-2 fw-bold"
                                            onclick="return confirm('¿Eliminar este ingrediente?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MODAL EDITAR --}}
                <div class="modal fade" id="editModal-{{ $ing['idIngrediente'] }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content shadow-lg border-0" style="border-radius: 20px;">
                            <div class="modal-header bg-warning text-dark border-0"
                                 style="border-radius: 20px 20px 0 0;">
                                <h5 class="modal-title fw-bold">
                                    <i class="fas fa-edit me-2"></i>Editar Ingrediente
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="{{ route('ingredientes.update', $ing['idIngrediente']) }}">
                                @csrf @method('PUT')
                                <div class="modal-body p-4">

                                    {{-- Nombre --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nombre</label>
                                        <input type="text" name="nombreIngrediente" class="form-control"
                                               value="{{ $ing['nombreIngrediente'] }}" required>
                                    </div>

                                    {{-- Referencia --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Referencia (SKU)</label>
                                        <input type="text" name="referenciaIngrediente" class="form-control"
                                               value="{{ $ing['referenciaIngrediente'] }}" required>
                                    </div>

                                    <div class="row">
                                        {{-- Proveedor --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Proveedor</label>
                                            <select name="idProveedor" class="form-select" required>
                                                @foreach($proveedores as $prov)
                                                    <option value="{{ $prov['idProveedor'] }}"
                                                        {{ $ing['idProveedor'] == $prov['idProveedor'] ? 'selected' : '' }}>
                                                        {{ $prov['nombreProv'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- Categoría --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Categoría</label>
                                            <select name="idCategoria" class="form-select" required>
                                                @foreach($categorias as $cat)
                                                    <option value="{{ $cat['idCategoriaIngrediente'] }}"
                                                        {{ $ing['idCategoria'] == $cat['idCategoriaIngrediente'] ? 'selected' : '' }}>
                                                        {{ $cat['nombreCategoria'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        {{-- Unidad de Medida ← NUEVO --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Unidad de Medida</label>
                                            <select name="idUnidadMedida" class="form-select" required>
                                                @foreach($unidades as $uni)
                                                    <option value="{{ $uni['idUnidad'] }}"
                                                        {{ ($ing['idUnidadMedida'] ?? 0) == $uni['idUnidad'] ? 'selected' : '' }}>
                                                        {{ $uni['nombreUnidad'] }} ({{ $uni['abreviaturaUnidad'] }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- Fecha Vencimiento ← NUEVO --}}
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Fecha de Vencimiento</label>
                                            <input type="date" name="fechaVencimiento" class="form-control"
                                                   value="{{ $ing['fechaVencimiento'] ?? '' }}">
                                        </div>
                                    </div>

                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-light px-4"
                                            data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-warning px-4 fw-bold">
                                        <i class="fas fa-save me-2"></i>Actualizar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                {{-- FIN MODAL EDITAR --}}

                @empty
                    <div class="col-12 text-center py-5">
                        <h4 class="text-muted">No hay ingredientes registrados.</h4>
                    </div>
                @endforelse
            </div>
            {{-- FIN GRID --}}

        </main>
    </div>
</div>

{{-- MODAL CREAR --}}
<div class="modal fade" id="crearModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0" style="border-radius: 20px;">
            <div class="modal-header text-white border-0"
                 style="background: var(--panaderia-marron-oscuro); border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-plus-circle me-2"></i>Nuevo Ingrediente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('ingredientes.store') }}">
                @csrf
                <div class="modal-body p-4">

                    {{-- Nombre --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre</label>
                        <input type="text" name="nombreIngrediente" class="form-control"
                               placeholder="Ej. Harina de Trigo" required>
                    </div>

                    {{-- Referencia --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Referencia (SKU)</label>
                        <input type="text" name="referenciaIngrediente" class="form-control"
                               placeholder="HAR-TRG-05" required>
                    </div>

                    <div class="row">
                        {{-- Proveedor --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Proveedor</label>
                            <select name="idProveedor" class="form-select" required>
                                <option value="" disabled selected>Elegir...</option>
                                @foreach($proveedores as $prov)
                                    <option value="{{ $prov['idProveedor'] }}">{{ $prov['nombreProv'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Categoría --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Categoría</label>
                            <select name="idCategoria" class="form-select" required>
                                <option value="" disabled selected>Elegir...</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat['idCategoriaIngrediente'] }}">
                                        {{ $cat['nombreCategoria'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Unidad de Medida ← NUEVO --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                Unidad de Medida <span class="text-danger">*</span>
                            </label>
                            <select name="idUnidadMedida" class="form-select" required>
                                <option value="" disabled selected>Elegir...</option>
                                @foreach($unidades as $uni)
                                    <option value="{{ $uni['idUnidad'] }}">
                                        {{ $uni['nombreUnidad'] }} ({{ $uni['abreviaturaUnidad'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Fecha Vencimiento ← NUEVO --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                Fecha de Vencimiento <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="fechaVencimiento" class="form-control" required>
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-panaderia px-4 fw-bold">
                        <i class="fas fa-save me-2"></i>Registrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- FIN MODAL CREAR --}}

@endsection

@push('scripts')
<script>
    document.getElementById('searchInput').addEventListener('keyup', function () {
        const value = this.value.toLowerCase();
        document.querySelectorAll('.ingrediente-item').forEach(item => {
            item.style.display = item.innerText.toLowerCase().includes(value) ? '' : 'none';
        });
    });
</script>
@endpush