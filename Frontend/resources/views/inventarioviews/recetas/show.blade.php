@extends('layouts.app')

@section('title', 'Detalle de Receta - El Castillo del Pan')

@push('styles')
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .main-content-scroll {
            height: 100vh;
            overflow-y: auto;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding-bottom: 5rem;
        }

        .recipe-header-panaderia {
            background: var(--panaderia-marron-oscuro, #2d1b0e);
            border-radius: 20px;
            color: white;
            padding: 2.5rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            box-shadow: var(--panaderia-shadow-lg);
        }

        .recipe-header-panaderia::after {
            content: "\f7ff";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            right: -20px;
            bottom: -20px;
            font-size: 10rem;
            opacity: 0.1;
            color: white;
        }

        .table-custom {
            border-radius: 15px;
            overflow: hidden;
            background: white;
        }

        .table-custom thead {
            background-color: var(--panaderia-beige-claro, #fdf8f3);
            color: var(--panaderia-marron-principal);
            border-bottom: 2px solid var(--panaderia-beige-oscuro);
        }

        .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,0.5); }

        .badge-cantidad {
            background: var(--panaderia-beige-claro);
            color: var(--panaderia-marron-oscuro);
            font-size: 1rem;
            font-weight: 700;
            border: 1px solid var(--panaderia-beige-oscuro);
        }

        .btn-panaderia {
            background: var(--panaderia-marron-principal);
            color: white;
            border-radius: 12px;
            transition: all 0.3s;
        }
        .btn-panaderia:hover { background: var(--panaderia-marron-hover); color: white; }

        .unidad-badge {
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            font-weight: bold;
            color: var(--panaderia-marron-oscuro);
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('components.admin-sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-4 main-content-scroll">

            {{-- Alertas --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-3 rounded-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mt-3 rounded-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Header --}}
            <div class="recipe-header-panaderia mt-4 shadow-sm animate__animated animate__fadeIn">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-2">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('recetas.index') }}" class="text-white-50 text-decoration-none">Recetas</a>
                                </li>
                                <li class="breadcrumb-item active text-white" aria-current="page">Ficha Técnica</li>
                            </ol>
                        </nav>
                        <h1 class="display-5 fw-bold mb-0">
                            {{ $detalles[0]['nombreProducto'] ?? 'Referencia #'.$idProducto }}
                        </h1>
                        <p class="mb-0 opacity-75">
                            <i class="fas fa-barcode me-2"></i>Código de Producto: {{ $idProducto }}
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn btn-warning btn-lg fw-bold px-4 shadow-sm rounded-pill"
                                data-bs-toggle="modal" data-bs-target="#editarModal">
                            <i class="fas fa-edit me-2"></i>Editar Ficha
                        </button>
                    </div>
                </div>
            </div>

            {{-- Tabla de ingredientes actuales --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden animate__animated animate__fadeInUp">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold" style="color: var(--panaderia-marron-principal);">
                        <i class="fas fa-mortar-pestle me-2"></i>Ingredientes y Proporciones
                    </h5>
                    <span class="badge bg-dark rounded-pill">{{ count($detalles) }} Insumos en total</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-custom">
                            <thead>
                                <tr>
                                    <th class="ps-4 py-3">Insumo / Ingrediente</th>
                                    <th class="text-center py-3">Cantidad Requerida</th>
                                    <th class="text-center py-3">Unidad</th>
                                    <th class="text-end pe-4 py-3">Ref. Interna</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detalles as $detalle)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle p-2 me-3 text-center"
                                                     style="width:40px;height:40px;background:#fff4e6;">
                                                    <i class="fas fa-wheat-awn text-warning"></i>
                                                </div>
                                                <div>
                                                    <span class="fw-bold d-block">{{ $detalle['nombreIngrediente'] }}</span>
                                                    <small class="text-muted">ID: {{ $detalle['idIngrediente'] }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-cantidad px-3 py-2 rounded-3">
                                                {{ number_format($detalle['cantidadRequerida'], 3) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold text-secondary">{{ $detalle['nombreUnidad'] }}</span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <code class="small text-muted">REC-{{ $detalle['idReceta'] }}</code>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-3 text-center">
                    <p class="small text-muted mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Esta ficha técnica es utilizada para el cálculo automático de insumos en el módulo de Producción.
                    </p>
                </div>
            </div>

            {{-- Botón volver --}}
            <div class="mt-4">
                <a href="{{ route('recetas.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Panel de Recetas
                </a>
            </div>

        </main>
    </div>
</div>

{{-- MODAL EDITAR --}}
<div class="modal fade" id="editarModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0" style="border-radius: 25px;">
            <div class="modal-header text-white border-0"
                 style="background: var(--panaderia-marron-oscuro); border-radius: 25px 25px 0 0;">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-edit me-2 text-warning"></i>Actualizar Ficha Técnica
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('recetas.update', $idProducto) }}">
                @csrf
                @method('PUT')

                <div class="modal-body p-4">
                    <p class="text-muted mb-4">
                        Editando receta de: <strong>{{ $detalles[0]['nombreProducto'] ?? 'Producto #'.$idProducto }}</strong>
                    </p>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0"><i class="fas fa-list me-2"></i>Composición</h6>
                        <button type="button" id="btnAgregarEditar" class="btn btn-sm btn-panaderia px-3 shadow-sm">
                            <i class="fas fa-plus me-1"></i>Agregar Insumo
                        </button>
                    </div>

                    <div id="contenedorEditar" class="p-3 rounded-4" style="background: rgba(0,0,0,0.02); border: 1px dashed #ccc;">
                        {{-- Filas precargadas con datos actuales --}}
                        @foreach($detalles as $i => $detalle)
                        <div class="row g-2 mb-3 align-items-end fila-ingrediente animate__animated animate__fadeIn">
                            <div class="col-md-5">
                                <label class="small fw-bold">Ingrediente</label>
                                <select name="detalles[{{ $i }}][idIngrediente]"
                                        class="form-select select-insumo-editar" required>
                                    <option value="">Seleccionar...</option>
                                    @foreach($ingredientes as $ing)
                                        <option value="{{ $ing['idIngrediente'] }}"
                                                data-uni="{{ $ing['abreviaturaUnidad'] }}"
                                                data-id-unidad="{{ $ing['idUnidad'] }}"
                                                {{ $ing['idIngrediente'] == $detalle['idIngrediente'] ? 'selected' : '' }}>
                                            {{ $ing['nombreIngrediente'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold">Cantidad</label>
                                <input type="number"
                                       name="detalles[{{ $i }}][cantidadRequerida]"
                                       class="form-control"
                                       step="0.001" min="0.001"
                                       value="{{ $detalle['cantidadRequerida'] }}"
                                       required>
                            </div>
                            <div class="col-md-2">
                                <label class="small fw-bold">Unidad</label>
                                <div class="unidad-badge unidad-display">{{ $detalle['nombreUnidad'] }}</div>
                                <input type="hidden"
                                       name="detalles[{{ $i }}][idUnidad]"
                                       class="input-unidad"
                                       value="{{ $detalle['idUnidad'] }}">
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button"
                                        class="btn btn-outline-danger btn-sm border-0 mb-1 btn-eliminar"
                                        title="Eliminar">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-panaderia px-5 fw-bold shadow">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Ingredientes disponibles desde PHP
    const insumosEditar = @json($ingredientes ?? []);

    // Índice inicial = cantidad de filas precargadas
    let indexEditar = {{ count($detalles) }};

    // ── Agregar nueva fila ──────────────────────────────────────────
    document.getElementById('btnAgregarEditar').addEventListener('click', function () {
        const contenedor = document.getElementById('contenedorEditar');

        let options = insumosEditar.map(i =>
            `<option value="${i.idIngrediente}" data-uni="${i.abreviaturaUnidad}" data-id-unidad="${i.idUnidad}">
                ${i.nombreIngrediente}
            </option>`
        ).join('');

        const div = document.createElement('div');
        div.className = 'row g-2 mb-3 align-items-end fila-ingrediente animate__animated animate__fadeIn';
        div.innerHTML = `
            <div class="col-md-5">
                <label class="small fw-bold">Ingrediente</label>
                <select name="detalles[${indexEditar}][idIngrediente]"
                        class="form-select select-insumo-editar" required>
                    <option value="">Seleccionar...</option>
                    ${options}
                </select>
            </div>
            <div class="col-md-4">
                <label class="small fw-bold">Cantidad</label>
                <input type="number"
                       name="detalles[${indexEditar}][cantidadRequerida]"
                       class="form-control" step="0.001" min="0.001"
                       placeholder="0.000" required>
            </div>
            <div class="col-md-2">
                <label class="small fw-bold">Unidad</label>
                <div class="unidad-badge unidad-display">---</div>
                <input type="hidden"
                       name="detalles[${indexEditar}][idUnidad]"
                       class="input-unidad" value="">
            </div>
            <div class="col-md-1 text-end">
                <button type="button"
                        class="btn btn-outline-danger btn-sm border-0 mb-1 btn-eliminar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        contenedor.appendChild(div);
        indexEditar++;
    });

    // ── Eliminar fila (delegado) ────────────────────────────────────
    document.getElementById('contenedorEditar').addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-eliminar');
        if (!btn) return;

        const filas = this.querySelectorAll('.fila-ingrediente');
        if (filas.length > 1) {
            btn.closest('.fila-ingrediente').remove();
            reindexarEditar();
        } else {
            alert('La receta debe tener al menos un ingrediente.');
        }
    });

    // ── Actualizar badge y idUnidad al cambiar select (delegado) ───
    document.getElementById('contenedorEditar').addEventListener('change', function (e) {
        if (!e.target.classList.contains('select-insumo-editar')) return;

        const option  = e.target.options[e.target.selectedIndex];
        const uni     = option.getAttribute('data-uni') || '---';
        const idUnidad = option.getAttribute('data-id-unidad') || '';

        const fila = e.target.closest('.fila-ingrediente');
        fila.querySelector('.unidad-display').textContent = uni;
        fila.querySelector('.input-unidad').value = idUnidad;
    });

    // ── Reindexar tras eliminar filas ──────────────────────────────
    function reindexarEditar() {
        document.querySelectorAll('#contenedorEditar .fila-ingrediente').forEach((fila, i) => {
            fila.querySelectorAll('[name]').forEach(input => {
                input.name = input.name.replace(/detalles\[\d+\]/, `detalles[${i}]`);
            });
        });
        indexEditar = document.querySelectorAll('#contenedorEditar .fila-ingrediente').length;
    }
</script>
@endpush