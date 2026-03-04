{{-- resources/views/inventarioviews/produccion/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Gestión de Producción - El Castillo del Pan')

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

        .tabla-historial thead {
            background: linear-gradient(45deg, var(--panaderia-marron-hover), var(--panaderia-marron-principal));
            color: white;
        }

        .tabla-historial tbody tr:hover {
            background: rgba(139, 111, 71, 0.05);
        }

        .badge-cantidad {
            background: linear-gradient(45deg, var(--panaderia-marron-principal), var(--panaderia-cafe-claro));
            color: white;
            font-size: 0.85rem;
            padding: 6px 14px;
            border-radius: 20px;
        }

        .btn-produccion {
            background: var(--panaderia-marron-principal);
            color: white;
            border: none;
            border-radius: var(--panaderia-radius-md);
            transition: all var(--panaderia-transition-normal);
        }

        .btn-produccion:hover {
            background: var(--panaderia-marron-hover);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--panaderia-shadow-lg);
        }

        .modal-content {
            border-radius: var(--panaderia-radius-xl) !important;
            border: none;
        }

        .modal-header-prod {
            background: linear-gradient(45deg, var(--panaderia-marron-hover), var(--panaderia-marron-principal));
            border-radius: var(--panaderia-radius-xl) var(--panaderia-radius-xl) 0 0;
            padding: 1.5rem 2rem;
        }

        .paso-card {
            background: white;
            border-radius: var(--panaderia-radius-lg);
            border: 1px solid var(--panaderia-beige-oscuro);
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .paso-numero {
            width: 32px;
            height: 32px;
            background: linear-gradient(45deg, var(--panaderia-marron-hover), var(--panaderia-marron-principal));
            color: white;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            margin-right: 10px;
        }

        .paso-titulo {
            color: var(--panaderia-marron-hover);
            font-weight: 700;
            font-size: 0.95rem;
        }

        .receta-tabla thead {
            background: var(--panaderia-beige-claro);
        }

        .receta-tabla th {
            color: var(--panaderia-marron-hover);
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
        }

        .receta-tabla td {
            font-size: 0.85rem;
        }

        #recetaPreview {
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">

        @include('components.admin-sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-4 main-content">

            {{-- Header --}}
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-2 mb-4 border-bottom">
                <div>
                    
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary border-0 me-3" style="color: #5d4037; font-size: 1.5rem; transition: transform 0.2s;">
            <i class="fas fa-arrow-left"></i>
        </a>
                <h1 class="h2 fw-bold" style="color: var(--panaderia-marron-hover);">
                        <i class="fas fa-industry me-2"></i>Gestión de Producción
                    </h1>
                    <p class="text-muted mb-0">Registro y reversión de producción de productos terminados.</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-produccion px-4 py-2 shadow-sm"
                            data-bs-toggle="modal" data-bs-target="#registrarModal">
                        <i class="fas fa-plus-circle me-2"></i>Nueva Producción
                    </button>
                    <a href="{{ route('produccion.index') }}" class="btn btn-outline-secondary px-3">
                        <i class="fas fa-sync"></i>
                    </a>
                </div>
            </div>

            {{-- Alertas --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4"
                     style="border-radius: var(--panaderia-radius-lg);">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4"
                     style="border-radius: var(--panaderia-radius-lg);">
                    <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Tabla Historial --}}
            <section class="mb-5">
                <h5 class="fw-bold mb-3" style="color: var(--panaderia-marron-hover);">
                    <i class="fas fa-history me-2"></i>Historial de Producción
                </h5>

                <div class="table-responsive shadow-sm rounded-3 overflow-hidden">
                    <table class="table tabla-historial align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="px-4 py-3">ID</th>
                                <th class="py-3">Producto</th>
                                <th class="py-3">Cantidad Producida</th>
                                <th class="py-3">Fecha y Hora</th>
                                <th class="py-3 text-center" style="width: 180px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @forelse($historial as $registro)
                                <tr>
                                    <td class="px-4 text-muted small">#{{ $registro['idProduccion'] }}</td>
                                    <td class="fw-bold" style="color: var(--panaderia-gris-oscuro);">
                                        {{ $registro['nombreProducto'] ?? 'ID: ' . $registro['idProducto'] }}
                                    </td>
                                    <td>
                                        <span class="badge-cantidad">
                                            {{ number_format($registro['cantidadProducida'], 2) }} uds
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        @if(isset($registro['fechaProduccion']))
                                            <i class="far fa-clock me-1"></i>
                                            {{ \Carbon\Carbon::parse($registro['fechaProduccion'])->format('d/m/Y H:i') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <form method="POST"
                                              action="{{ route('produccion.destroy', $registro['idProduccion']) }}"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-outline-danger btn-sm fw-bold"
                                                    onclick="return confirm('ADVERTENCIA: ¿Eliminar y REVERTIR los cambios de inventario? Esta acción no se puede deshacer.')">
                                                <i class="fas fa-undo me-1"></i>Revertir
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-box-open fa-2x mb-2 d-block opacity-25"></i>
                                        No hay registros de producción.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

        </main>
    </div>
</div>

{{-- MODAL REGISTRAR PRODUCCIÓN --}}
<div class="modal fade" id="registrarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg">

            <div class="modal-header-prod text-white">
                <h5 class="modal-title fw-bold mb-0">
                    <i class="fas fa-industry me-2"></i>Registrar Nueva Producción
                </h5>
                <button type="button" class="btn-close btn-close-white ms-auto"
                        data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('produccion.store') }}">
                @csrf
                <div class="modal-body p-4" style="background: var(--panaderia-beige-claro);">

                    {{-- PASO 1: Seleccionar receta --}}
                    <div class="paso-card">
                        <div class="d-flex align-items-center mb-3">
                            <span class="paso-numero">1</span>
                            <span class="paso-titulo">Seleccionar Producto a Fabricar</span>
                        </div>

                        <label class="form-label fw-bold small text-uppercase"
                               style="color: var(--panaderia-gris-texto);">
                            Receta / Producto
                        </label>
                        <select id="selectReceta" class="form-select mb-3" required>
                            <option value="" disabled selected>Elegir receta registrada...</option>
                            @forelse($productosConReceta as $prod)
                                <option value="{{ $prod['idProducto'] }}">
                                    {{ $prod['nombreProducto'] ?? 'Producto ID ' . $prod['idProducto'] }}
                                </option>
                            @empty
                                <option disabled>No hay recetas registradas</option>
                            @endforelse
                        </select>

                        <input type="hidden" name="idProducto" id="idProductoHidden">

                        {{-- Preview ingredientes --}}
                        <div id="recetaPreview" class="d-none mt-3">
                            <p class="small fw-bold text-uppercase mb-2"
                               style="color: var(--panaderia-gris-texto);">
                                <i class="fas fa-list me-1"></i>Ingredientes que se consumirán
                            </p>
                            <div class="table-responsive">
                                <table class="table table-sm receta-tabla rounded-3 overflow-hidden mb-0 border">
                                    <thead>
                                        <tr>
                                            <th class="px-3 py-2">Ingrediente</th>
                                            <th class="text-center py-2">Cant. por unidad</th>
                                            <th class="text-center py-2">Unidad</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recetaIngredientesBody"></tbody>
                                </table>
                            </div>
                            <div class="alert small py-2 mt-2 mb-0"
                                 style="background: var(--panaderia-beige-oscuro);
                                        border-radius: var(--panaderia-radius-sm);
                                        color: var(--panaderia-marron-hover);
                                        border: 1px solid var(--panaderia-cafe-claro);">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Las cantidades son por <strong>cada unidad producida</strong>.
                                El sistema multiplicará por la cantidad del paso 2.
                            </div>
                        </div>
                    </div>

                    {{-- PASO 2: Cantidad --}}
                    <div class="paso-card mb-0">
                        <div class="d-flex align-items-center mb-3">
                            <span class="paso-numero">2</span>
                            <span class="paso-titulo">Cantidad a Producir</span>
                        </div>

                        <div class="row align-items-end g-3">
                            <div class="col-md-5">
                                <label class="form-label fw-bold">Unidades a fabricar</label>
                                <input type="number" step="0.01"
                                       name="cantidadProducida"
                                       id="cantidadProducida"
                                       class="form-control form-control-lg"
                                       min="0.01"
                                       placeholder="Ej: 10"
                                       value="{{ old('cantidadProducida') }}"
                                       required>
                                @error('cantidadProducida')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-7">
                                <div class="alert small py-2 mb-0"
                                     style="background: var(--panaderia-beige-oscuro);
                                            border-radius: var(--panaderia-radius-sm);
                                            color: var(--panaderia-marron-hover);
                                            border: 1px solid var(--panaderia-cafe-claro);">
                                    <i class="fas fa-info-circle me-1"></i>
                                    El sistema descontará ingredientes automáticamente
                                    según la receta seleccionada.
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-0 bg-white px-4 pb-4"
                     style="border-radius: 0 0 var(--panaderia-radius-xl) var(--panaderia-radius-xl);">
                    <button type="button" class="btn btn-light px-4"
                            data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnRegistrar"
                            class="btn btn-produccion px-4 fw-bold" disabled>
                        <i class="fas fa-save me-2"></i>Registrar Producción
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const todasLasRecetas        = @json($recetas ?? []);
    const selectReceta           = document.getElementById('selectReceta');
    const idProductoHidden       = document.getElementById('idProductoHidden');
    const recetaPreview          = document.getElementById('recetaPreview');
    const recetaIngredientesBody = document.getElementById('recetaIngredientesBody');
    const btnRegistrar           = document.getElementById('btnRegistrar');

    selectReceta.addEventListener('change', function () {
        const idProducto = this.value;
        idProductoHidden.value = idProducto;

        const ingredientes = todasLasRecetas.filter(
            r => String(r.idProducto) === String(idProducto)
        );

        if (ingredientes.length > 0) {
            recetaIngredientesBody.innerHTML = ingredientes.map(ing => `
                <tr>
                    <td class="px-3 fw-bold">
                        ${ing.nombreIngrediente ?? 'ID ' + ing.idIngrediente}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark border">
                            ${parseFloat(ing.cantidadRequerida).toFixed(2)}
                        </span>
                    </td>
                    <td class="text-center text-muted">
                        ${ing.nombreUnidad ?? '—'}
                    </td>
                </tr>
            `).join('');

            recetaPreview.classList.remove('d-none');
            btnRegistrar.disabled = false;
        } else {
            recetaPreview.classList.add('d-none');
            btnRegistrar.disabled = true;
        }
    });

    @if($errors->any())
        const modalEl = document.getElementById('registrarModal');
        if (modalEl) new bootstrap.Modal(modalEl).show();
    @endif

});
</script>
@endpush