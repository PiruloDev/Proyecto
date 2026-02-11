@extends('layouts.app') 

@section('title', 'Gestión de Recetas')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('components.admin-sidebar') 
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
            {{-- Encabezado --}}
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2"><i class="fas fa-utensils me-2 text-primary"></i>Gestión de Recetas</h1>
                <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fas fa-plus-circle me-1"></i> Nueva Receta
                </button>
            </div>

            {{-- Alertas --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            {{-- Listado de Recetas --}}
            <section id="listado-recetas" class="mb-5">
                <div class="row g-4">
                    @forelse($recetas as $receta)
                        <div class="col-md-6 col-xl-4">
                            <div class="card h-100 shadow-sm border-0 border-top border-primary border-4">
                                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-uppercase text-muted fw-bold italic" style="font-size: 0.7rem;">Producto Terminado</small>
                                        <h5 class="card-title mb-0 fw-bold text-dark">{{ $receta['nombreProducto'] }}</h5>
                                        <span class="badge bg-light text-primary border border-primary mt-1">ID: {{ $receta['idProducto'] }}</span>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                            <li><a class="dropdown-item" href="{{ route('recetas.show', $receta['idProducto']) }}"><i class="fas fa-eye me-2 text-info"></i>Ver detalle</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('recetas.destroy', $receta['idProducto']) }}" method="POST" onsubmit="return confirm('¿Eliminar esta receta permanentemente?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash me-2"></i>Eliminar</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="ps-3 py-2 small">Ingrediente</th>
                                                    <th class="text-end pe-3 py-2 small">Cantidad</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(array_slice($receta['detalles'], 0, 4) as $detalle)
                                                    <tr>
                                                        <td class="ps-3 py-2 small text-secondary">
                                                            <i class="fas fa-circle me-1 text-info" style="font-size: 0.4rem;"></i>
                                                            ID {{ $detalle['idIngrediente'] }}
                                                        </td>
                                                        <td class="text-end pe-3 py-2 fw-bold small">
                                                            {{ number_format($detalle['cantidadRequerida'], 3) }} 
                                                            <span class="text-muted fw-normal">u.</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @if(count($receta['detalles']) > 4)
                                        <div class="text-center py-2 bg-light border-top">
                                            <small class="text-muted">+ {{ count($receta['detalles']) - 4 }} ingredientes más</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-5 bg-white rounded shadow-sm">
                                <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png" width="80" class="mb-3 opacity-50">
                                <h5 class="text-muted">No hay recetas registradas</h5>
                                <p class="small text-secondary">Comience creando una nueva receta para sus productos.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- MODAL CREAR RECETA --}}
            <div class="modal fade" id="crearModal" data-bs-backdrop="static" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered shadow-lg">
                    <div class="modal-content border-0">
                        <div class="modal-header bg-dark text-white border-0">
                            <h5 class="modal-title fw-bold"><i class="fas fa-folder-plus me-2 text-primary"></i>Nueva Ficha Técnica (Receta)</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <form method="POST" action="{{ route('recetas.store') }}" id="recetaForm">
                            @csrf
                            <div class="modal-body p-4">
                                {{-- Sección Producto --}}
                                <div class="row mb-4 p-3 bg-light rounded border">
                                    <div class="col-12">
                                        <label for="idProducto" class="form-label fw-bold text-dark">Producto a Producir</label>
                                        <select id="idProducto" name="idProducto" class="form-select border-primary" required>
                                            <option value="">-- Seleccione un producto del catálogo --</option>
                                            @foreach($productos as $prod)
                                                <option value="{{ $prod->ID_PRODUCTO }}" {{ old('idProducto') == $prod->ID_PRODUCTO ? 'selected' : '' }}>
                                                    {{ $prod->NOMBRE_PRODUCTO }} (ID: {{ $prod->ID_PRODUCTO }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text mt-2 italic small"><i class="fas fa-info-circle me-1"></i>Solo aparecerán productos registrados en el inventario.</div>
                                    </div>
                                </div>

                                {{-- Sección Ingredientes --}}
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold mb-0 text-primary uppercase"><i class="fas fa-list me-2"></i>Composición de Ingredientes</h6>
                                    <button type="button" id="add-detail-btn" class="btn btn-outline-success btn-sm rounded-pill px-3">
                                        <i class="fas fa-plus me-1"></i> Agregar
                                    </button>
                                </div>
                                
                                <div id="detalles-container" class="border rounded bg-white">
                                    {{-- Se llena con JS --}}
                                </div>
                            </div>

                            <div class="modal-footer bg-light border-0">
                                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary px-5 shadow">
                                    <i class="fas fa-save me-2"></i>Registrar Receta
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('detalles-container');
            const addButton = document.getElementById('add-detail-btn');
            let detailIndex = 0;

            // Datos del catálogo de ingredientes desde Blade a JS
            const ingredientesCatalogo = @json($ingredientesCatalogo);

            function addDetailRow(index, data = null) {
                const currentIndex = index !== null ? index : detailIndex;
                const newRow = document.createElement('div');
                newRow.className = 'row g-2 detalle-row p-3 border-bottom align-items-end mx-0';
                
                // Generar las opciones del select
                let optionsHtml = '<option value="">-- Seleccionar --</option>';
                ingredientesCatalogo.forEach(ing => {
                    const selected = (data && data.idIngrediente == ing.idIngrediente) ? 'selected' : '';
                    optionsHtml += `<option value="${ing.idIngrediente}" ${selected}>${ing.nombreIngrediente}</option>`;
                });

                newRow.innerHTML = `
                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-secondary">Ingrediente</label>
                        <select name="ingredientes[${currentIndex}][idIngrediente]" class="form-select form-select-sm shadow-sm" required>
                            ${optionsHtml}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-secondary">Cantidad Requerida</label>
                        <input type="number" step="0.001" name="ingredientes[${currentIndex}][cantidadNecesaria]" 
                               class="form-control form-control-sm shadow-sm" required min="0.001" 
                               placeholder="0.000" value="${data ? data.cantidadNecesaria : ''}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-secondary">Unidad (ID)</label>
                        <input type="number" name="ingredientes[${currentIndex}][idUnidad]" 
                               class="form-control form-control-sm shadow-sm text-center" required 
                               placeholder="Ej: 1" value="${data ? data.idUnidad : '1'}">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger btn-sm border-0 remove-detail-btn mb-1" title="Quitar">
                            <i class="fas fa-times-circle fa-lg"></i>
                        </button>
                    </div>
                `;
                container.appendChild(newRow);
                if (index === null) detailIndex++;
                updateRemoveButtons();
            }

            function updateRemoveButtons() {
                const buttons = container.querySelectorAll('.remove-detail-btn');
                buttons.forEach(btn => btn.style.visibility = (container.children.length > 1) ? 'visible' : 'hidden');
            }

            // Manejo de errores de validación (Old data)
            const oldDetalles = @json(old('ingredientes') ?: []);
            if (oldDetalles.length > 0) {
                oldDetalles.forEach((det, i) => addDetailRow(i, det));
                detailIndex = oldDetalles.length;
            } else {
                addDetailRow(null); // Fila inicial
            }

            addButton.addEventListener('click', () => addDetailRow(null));

            container.addEventListener('click', (e) => {
                if (e.target.closest('.remove-detail-btn')) {
                    e.target.closest('.detalle-row').remove();
                    updateRemoveButtons();
                }
            });

            // Si hay errores, re-abrir modal
            @if($errors->any())
                new bootstrap.Modal(document.getElementById('crearModal')).show();
            @endif
        });
    </script>
@endpush