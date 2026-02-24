{{-- resources/views/inventarioviews/produccion/index.blade.php (Ajustado a Dashboard) --}}

@extends('layouts.app')

@section('title', 'Gestión de Producción - El Castillo del Pan')

@push('styles')
    {{-- Estilos necesarios para la integración con el layout de dashboard --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet"> {{-- Aseguramos que se incluyan --}}
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet"> {{-- Aseguramos que se incluyan --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        
        {{-- Side Bar: Incluye el sidebar de administrador --}}
        @include('components.admin-sidebar') 
        
        {{-- CONTENIDO PRINCIPAL --}}
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
            
            {{-- Título y Controles --}}
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2"><i class="fas fa-industry"></i> Gestión de Producción</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    {{-- Espacio para botones secundarios o filtros si los hubiera --}}
                </div>
            </div>

            {{-- MENSAJES --}}
            @if (session('success'))
                <div class="alert alert-success my-3 shadow-sm"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger my-3 shadow-sm"><i class="fas fa-times-circle"></i> {{ session('error') }}</div>
            @endif
            
            {{-- Botones de Acción Principal --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <button class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#registrarModal">
                    <i class="fas fa-plus-circle"></i> Registrar Nueva Producción
                </button>
                <a href="{{ route('produccion.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-sync"></i> Recargar Historial
                </a>
            </div>
            
            {{-- LISTADO / HISTORIAL DE PRODUCCIÓN --}}
            <section id="historial" class="mb-5">
                <h3 class="mb-3 text-secondary">Historial de Producción Registrada</h3>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle shadow-sm rounded-3">
                        <thead class="table-dark"> {{-- Cabecera oscura para más impacto --}}
                            <tr>
                                <th>ID Producción</th>
                                <th>Producto</th>
                                <th>Cantidad Producida</th>
                                <th>Fecha y Hora</th>
                                <th style="width: 200px;" class="text-center">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($historial as $registro)
                                <tr>
                                    <td>{{ $registro['idProduccion'] }}</td>
                                    <td>{{ $registro['nombreProducto'] ?? 'ID: ' . $registro['idProducto'] }}</td>

                                    <td><span class="badge bg-info text-dark">{{ number_format($registro['cantidadProducida'], 2) }}</span></td> 
                                    <td>
                                        @if(isset($registro['fechaProduccion']))
                                            {{ \Carbon\Carbon::parse($registro['fechaProduccion'])->format('Y-m-d H:i:s') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        {{-- BOTÓN ELIMINAR (Reversión de Inventario) --}}
                                        <form method="POST"
                                            action="{{ route('produccion.destroy', $registro['idProduccion']) }}"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('ADVERTENCIA: ¿Eliminar y REVERTIR los cambios de inventario (ingredientes y stock)? Esta acción no se puede deshacer.')"
                                                title="Revertir/Eliminar Producción">
                                                <i class="fas fa-undo"></i> Revertir / Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center p-3">
                                        <i class="fas fa-info-circle me-2"></i> No hay registros de producción en el historial.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- MODAL REGISTRAR PRODUCCIÓN --}}
            <section>
                <div class="modal fade" id="registrarModal" tabindex="-1">
                    <div class="modal-dialog modal-lg"> {{-- Se redujo un poco el tamaño, XXL no parece necesario --}}
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title"><i class="fas fa-plus-square"></i> Registrar Nueva Producción</h5>
                                <button class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <form method="POST" action="{{ route('produccion.store') }}" class="row g-3 p-4">
                                @csrf

                                {{-- Detalles de la Producción --}}
                                <h4 class="mb-3 text-primary border-bottom pb-2"><i class="fas fa-bread-slice"></i> Producto Terminado</h4>
                                <div class="row g-3 mb-4 p-3 rounded-3 bg-light border"> {{-- Se usa bg-light para resaltar --}}
                                    <div class="col-md-6">
                                        <label for="idProducto" class="form-label fw-bold">ID Producto Terminado</label>
                                        <input type="number" id="idProducto" name="idProducto" class="form-control" required value="{{ old('idProducto') }}" placeholder="Ej: 101">
                                        @error('idProducto')<div class="text-danger small">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="cantidadProducida" class="form-label fw-bold">Cantidad Producida</label>
                                        <input type="number" step="0.01" id="cantidadProducida" name="cantidadProducida" class="form-control" required min="0.01" value="{{ old('cantidadProducida') }}" placeholder="Ej: 50.50">
                                        @error('cantidadProducida')<div class="text-danger small">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                
                                <hr class="my-3">
                                
                                {{-- Descuento Manual de Ingredientes --}}
                                <h4 class="mb-3 text-success"><i class="fas fa-minus-circle"></i> Descuento Manual de Ingredientes (Opcional)</h4>
                                <div class="alert alert-info small py-2">
                                    Si no especifica ingredientes aquí, el sistema intentará descontar automáticamente los ingredientes basados en la **Receta** asociada al Producto ID.
                                </div>

                                <div id="detalles-container">
                                    {{-- Se mantiene la lógica de rellenar con old() para la persistencia de datos --}}
                                    @if(old('ingredientesDescontados'))
                                        @foreach(old('ingredientesDescontados') as $index => $detalle)
                                            <div class="row g-3 detalle-row mb-2 border p-3 bg-white rounded-3 shadow-sm" data-index="{{ $index }}">
                                                <div class="col-md-5">
                                                    <label class="form-label small">ID Ingrediente</label>
                                                    <input type="number" name="ingredientesDescontados[{{ $index }}][idIngrediente]" class="form-control form-control-sm" required value="{{ $detalle['idIngrediente'] ?? '' }}" placeholder="ID Ingrediente">
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label small">Cantidad Usada</label>
                                                    <input type="number" step="0.01" name="ingredientesDescontados[{{ $index }}][cantidadUsada]" class="form-control form-control-sm" required min="0.01" value="{{ $detalle['cantidadUsada'] ?? '' }}" placeholder="Cantidad">
                                                </div>
                                                <div class="col-md-2 d-flex align-items-end">
                                                    <button type="button" class="btn btn-danger w-100 btn-sm remove-detail-btn" title="Eliminar este ingrediente">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                
                                <div class="col-12 text-end mt-3">
                                    <button type="button" id="add-detail-btn" class="btn btn-sm btn-success shadow-sm">
                                        <i class="fas fa-plus"></i> Agregar Ingrediente Manual
                                    </button>
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary w-100 btn-lg shadow">
                                        <i class="fas fa-save"></i> Registrar Producción
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

        </main>
    </div>
</div>
@endsection

@push('scripts')
    {{-- Script para manejar la adición y remoción dinámica de campos de ingredientes --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('detalles-container');
            const addButton = document.getElementById('add-detail-btn');
            
            // Función para obtener el índice más alto y calcular el siguiente.
            // Esto es crucial para manejar correctamente los arrays de inputs de Laravel/PHP.
            function getNextIndex() {
                const rows = container.querySelectorAll('.detalle-row');
                if (rows.length === 0) {
                    return 0;
                }
                
                let maxIndex = -1;
                rows.forEach(row => {
                    const index = parseInt(row.dataset.index);
                    if (!isNaN(index) && index > maxIndex) {
                        maxIndex = index;
                    }
                });
                return maxIndex + 1;
            }

            function addDetailRow() {
                const detailIndex = getNextIndex();
                const newRow = document.createElement('div');
                newRow.className = 'row g-3 detalle-row mb-2 border p-3 bg-white rounded-3 shadow-sm';
                newRow.setAttribute('data-index', detailIndex);

                newRow.innerHTML = `
                    <div class="col-md-5">
                        <label class="form-label small">ID Ingrediente</label>
                        <input type="number" name="ingredientesDescontados[${detailIndex}][idIngrediente]" class="form-control form-control-sm" required placeholder="ID Ingrediente">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small">Cantidad Usada</label>
                        <input type="number" step="0.01" name="ingredientesDescontados[${detailIndex}][cantidadUsada]" class="form-control form-control-sm" required min="0.01" placeholder="Cantidad">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger w-100 btn-sm remove-detail-btn" title="Eliminar este ingrediente">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                `;
                container.appendChild(newRow);
            }

            // Listener para agregar filas
            addButton.addEventListener('click', addDetailRow);

            // Listener para eliminar filas
            container.addEventListener('click', function(e) {
                if (e.target.closest('.remove-detail-btn')) {
                    const rowToRemove = e.target.closest('.detalle-row');
                    if (rowToRemove) {
                         rowToRemove.remove();
                    }
                }
            });
            
            // Mostrar modal si hubo errores de validación
            @php
                if(session()->has('errors') && $errors->any()) {
                    echo "const modalElement = document.getElementById('registrarModal');\n";
                    echo "if(modalElement) {\n";
                    echo "    const modal = new bootstrap.Modal(modalElement);\n";
                    echo "    modal.show();\n";
                    echo "}\n";
                }
            @endphp
        });
    </script>
@endpush