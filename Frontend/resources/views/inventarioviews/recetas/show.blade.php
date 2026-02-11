@extends('layouts.app')

@section('title', 'Detalle de Receta - ' . ($producto->NOMBRE_PRODUCTO ?? 'Producto #' . $idProducto))

@push('styles')
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .recipe-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            border-radius: 15px;
            color: white;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .table-custom thead {
            background-color: #f8fafc;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        {{-- Sidebar de Administración --}}
        @include('components.admin-sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
            {{-- Encabezado de la Receta --}}
            <div class="recipe-header mt-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-2">
                                <li class="breadcrumb-item"><a href="{{ route('recetas.index') }}" class="text-white-50">Recetas</a></li>
                                <li class="breadcrumb-item active text-white" aria-current="page">Ficha Técnica</li>
                            </ol>
                        </nav>
                        <h1 class="display-6 fw-bold mb-0">
                            {{ $producto->NOMBRE_PRODUCTO ?? 'Producto #' . $idProducto }}
                        </h1>
                        <p class="lead opacity-75 mb-0">ID de Referencia: {{ $idProducto }}</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn btn-warning shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#editarModal">
                            <i class="fas fa-edit me-2"></i>Editar Ficha
                        </button>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <a href="{{ route('recetas.index') }}" class="btn btn-link text-decoration-none p-0 text-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Volver al listado principal
                </a>
            </div>

            {{-- Listado de Ingredientes --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 text-dark fw-bold">
                        <i class="fas fa-microchip me-2 text-primary"></i>Componentes Requeridos
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if (empty($detalles))
                        <div class="text-center py-5">
                            <i class="fas fa-info-circle fa-3x text-light mb-3"></i>
                            <p class="text-muted">No se han definido ingredientes para esta receta.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 table-custom">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Ingrediente</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-center">Unidad</th>
                                        <th class="text-end pe-4">ID Registro</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($detalles as $detalle)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-light rounded-circle p-2 me-3 text-center" style="width: 35px">
                                                        <i class="fas fa-flask text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <span class="fw-semibold d-block">ID: {{ $detalle['idIngrediente'] }}</span>
                                                        <small class="text-muted">Materia Prima</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success-subtle text-success fs-6 px-3 py-2 rounded-pill">
                                                    {{ number_format($detalle['cantidadRequerida'], 3) }}
                                                </span>
                                            </td>
                                            <td class="text-center text-secondary">
                                                {{ $detalle['idUnidad'] }} <small>(ID)</small>
                                            </td>
                                            <td class="text-end pe-4">
                                                <span class="text-monospace text-muted small">#{{ $detalle['idReceta'] }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-light border-0 py-3">
                    <small class="text-muted italic">
                        <i class="fas fa-exclamation-triangle me-1"></i> Estas cantidades son calculadas por unidad de producto terminado.
                    </small>
                </div>
            </div>
        </main>
    </div>
</div>

{{-- MODAL EDITAR --}}
<div class="modal fade" id="editarModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered shadow-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-tools me-2 text-warning"></i>Modificar Ficha Técnica</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('recetas.update', $idProducto) }}">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                        <div>
                            <strong>Atención:</strong> Al actualizar, se reemplazarán todos los ingredientes actuales por la nueva lista definida aquí.
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 mt-4 text-uppercase text-secondary" style="font-size: 0.8rem;">Listado de Ingredientes</h6>
                    <div id="edit-detalles-container" class="rounded border bg-light">
                        {{-- Se llena con JS --}}
                    </div>

                    <div class="mt-3">
                        <button type="button" id="edit-add-detail-btn" class="btn btn-outline-primary btn-sm rounded-pill">
                            <i class="fas fa-plus me-1"></i> Añadir Ingrediente
                        </button>
                    </div>
                </div>

                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning px-4 shadow">
                        <i class="fas fa-sync-alt me-2"></i>Actualizar Receta
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
        const modal = document.getElementById('editarModal');
        const container = document.getElementById('edit-detalles-container');
        const addButton = document.getElementById('edit-add-detail-btn');
        const initialDetalles = @json($detalles);
        
        let detailIndex = 0;

        function addDetailRow(index, data = null) {
            const currentIndex = index !== null ? index : detailIndex;
            const newRow = document.createElement('div');
            newRow.className = 'row g-2 detalle-row p-3 border-bottom mx-0 align-items-end';

            newRow.innerHTML = `
                <div class="col-md-5">
                    <label class="form-label small fw-bold">ID Ingrediente</label>
                    <input type="number" name="ingredientes[${currentIndex}][idIngrediente]" class="form-control form-control-sm" required value="${data ? data.idIngrediente : ''}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Cantidad</label>
                    <input type="number" step="0.001" name="ingredientes[${currentIndex}][cantidadNecesaria]" class="form-control form-control-sm" required value="${data ? (data.cantidadRequerida || data.cantidadNecesaria) : ''}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Unidad (ID)</label>
                    <input type="number" name="ingredientes[${currentIndex}][idUnidad]" class="form-control form-control-sm" required value="${data ? data.idUnidad : '1'}">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-link text-danger remove-detail-btn pb-1">
                        <i class="fas fa-times-circle fa-lg"></i>
                    </button>
                </div>
            `;
            container.appendChild(newRow);
            if (index === null) detailIndex++;
            updateRemoveButtons();
        }

        function updateRemoveButtons() {
            const btns = container.querySelectorAll('.remove-detail-btn');
            btns.forEach(btn => btn.style.display = (container.children.length > 1) ? 'block' : 'none');
        }

        modal.addEventListener('show.bs.modal', function () {
            container.innerHTML = '';
            if (initialDetalles.length > 0) {
                initialDetalles.forEach((d, i) => addDetailRow(i, d));
                detailIndex = initialDetalles.length;
            } else {
                addDetailRow(null);
            }
        });

        addButton.addEventListener('click', () => addDetailRow(null));

        container.addEventListener('click', (e) => {
            if (e.target.closest('.remove-detail-btn')) {
                e.target.closest('.detalle-row').remove();
                updateRemoveButtons();
            }
        });
    });
</script>
@endpush