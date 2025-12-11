@extends('layouts.app') 

@section('title', 'Gestión de Recetas')

@push('styles')
    {{-- Asegúrate de que los estilos del dashboard y Font Awesome estén cargados --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    {{-- Si el estilo 'stylemoduloinv.css' es crucial, inclúyelo --}}
    {{-- <link rel="stylesheet" href="{{ asset('css/stylemoduloinv.css') }}"> --}}
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        
        {{-- Side Bar: Asumo que ahora es un componente o partial estandarizado --}}
        @include('components.admin-sidebar') 
        
        {{-- Contenido Principal --}}
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
            
            {{-- Título y Botón de Navegación --}}
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2">Gestión de Recetas</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
        
                </div>
            </div>

            {{-- MENSAJES DE SESIÓN --}}
            @if (session('success'))
                <div class="alert alert-success my-3">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger my-3">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger my-3">
                    Hay errores en el formulario de creación. Por favor, revísalos.
                </div>
            @endif
            
            {{-- Botón para abrir el modal de creación --}}
            <button class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#crearModal">
                <i class="fas fa-plus-circle"></i> Crear Nueva Receta
            </button>
                    
            <section id="listado-recetas" class="mb-5">
                <h3 class="mb-3 text-secondary">Listado de Recetas por Producto</h3>

                @forelse($recetas as $receta)
                    <div class="card shadow-sm mb-4 border-primary">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-2">
                            <h5 class="my-0">
                                <i class="fas fa-cookie-bite"></i> Receta: 
                                <strong>{{ $receta['nombreProducto'] }}</strong> (ID Producto: {{ $receta['idProducto'] }})
                            </h5>
                            <div class="btn-group" role="group">
                                {{-- Botón VER DETALLE --}}
                                <a href="{{ route('recetas.show', $receta['idProducto']) }}" 
                                    class="btn btn-sm btn-light me-2" title="Ver Detalles">
                                    <i class="fas fa-search"></i>
                                </a>
                                
                                {{-- Botón ELIMINAR --}}
                                <form method="POST"
                                    action="{{ route('recetas.destroy', $receta['idProducto']) }}"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('ADVERTENCIA: ¿Eliminar la receta del Producto ID {{ $receta['idProducto'] }}? Esto no se puede deshacer.')"
                                        title="Eliminar Receta">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <h6 class="p-3 mb-0 bg-light border-bottom">Ingredientes Requeridos:</h6>
                            <ul class="list-group list-group-flush">
                                @foreach($receta['detalles'] as $detalle)
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <span>
                                            <i class="fas fa-tag text-info me-2"></i> 
                                            Ingrediente ID {{ $detalle['idIngrediente'] }}: <span class="fw-bold">{{ number_format($detalle['cantidadRequerida'], 3) }}</span> 
                                            (Unidad ID: {{ $detalle['idUnidad'] }})
                                        </span>
                                        <small class="text-muted">Detalle ID: {{ $detalle['idReceta'] }}</small>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-triangle"></i> No hay recetas registradas en el sistema.
                    </div>
                @endforelse
            </section>

            {{-- MODAL CREAR RECETA --}}
            <div class="modal fade" id="crearModal" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title"><i class="fas fa-plus"></i> Crear Nueva Receta de Producto</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <form method="POST" action="{{ route('recetas.store') }}" class="row g-3 p-4">
                            @csrf

                            <h3 class="border-bottom pb-2">Datos del Producto</h3>
                            <div class="col-12 mb-3">
                                <label for="idProducto" class="form-label fw-bold">ID Producto Terminado (Debe existir en la tabla de productos)</label>
                                <input type="number" id="idProducto" name="idProducto" class="form-control" required value="{{ old('idProducto') }}">
                                @error('idProducto')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            
                            <h4 class="border-bottom pb-2 mt-4">Ingredientes Requeridos</h4>
                            <p class="text-muted">Añada cada ingrediente y su cantidad exacta para producir una unidad del producto.</p>
                            
                            <div id="detalles-container" class="col-12">
                                {{-- Contenedor para los campos dinámicos (manejo JS) --}}
                            </div>
                            
                            <div class="col-12 text-end">
                                <button type="button" id="add-detail-btn" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Agregar Ingrediente
                                </button>
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save"></i> Guardar Receta Completa
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
    {{-- SCRIPT PARA MANEJAR CAMPOS DINÁMICOS DE RECETA --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('detalles-container');
            const addButton = document.getElementById('add-detail-btn');
            
            let detailIndex = 0;
            
            // Cargar datos viejos en caso de error de validación
            const oldDetallesJson = container.getAttribute('data-old-detalles') || '[]';
            const oldDetalles = {!! htmlspecialchars_decode(json_encode(old('ingredientes') ?: [])) !!};
            
            if (oldDetalles.length > 0) {
                oldDetalles.forEach((detalle, index) => {
                    // Asegurar que el index es un número válido si viene de old()
                    addDetailRow(index, detalle); 
                });
                detailIndex = oldDetalles.length;
            } else {
                // Agregar una fila inicial si no hay datos de error
                addDetailRow(0, null);
                detailIndex = 1;
            }
            
            function addDetailRow(index, data = null) {
                const currentIndex = index !== null ? index : detailIndex;
                
                const newRow = document.createElement('div');
                newRow.className = 'row g-3 detalle-row align-items-center p-2 border-bottom';
                newRow.setAttribute('data-index', currentIndex);

                const idIngredienteVal = data ? (data.idIngrediente || '') : '';
                const cantidadVal = data ? (data.cantidadNecesaria || '') : '';
                const idUnidadVal = data ? (data.idUnidad || '') : '';

                newRow.innerHTML = `
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">ID Ingrediente</label>
                        <input type="number" name="ingredientes[${currentIndex}][idIngrediente]" class="form-control form-control-sm" required value="${idIngredienteVal}">
                        @error('ingredientes.${currentIndex}.idIngrediente')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Cantidad Necesaria</label>
                        <input type="number" step="0.001" name="ingredientes[${currentIndex}][cantidadNecesaria]" class="form-control form-control-sm" required min="0.001" value="${cantidadVal}">
                        @error('ingredientes.${currentIndex}.cantidadNecesaria')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">ID Unidad</label>
                        <input type="number" name="ingredientes[${currentIndex}][idUnidad]" class="form-control form-control-sm" required value="${idUnidadVal}">
                        @error('ingredientes.${currentIndex}.idUnidad')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-1 d-flex align-items-center pt-3">
                        <button type="button" class="btn btn-danger btn-sm w-100 remove-detail-btn" title="Eliminar Ingrediente">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                `;
                container.appendChild(newRow);
                updateRemoveButtons();

                if (index === null) {
                    detailIndex++;
                }
            }

            function updateRemoveButtons() {
                const removeButtons = container.querySelectorAll('.remove-detail-btn');
                // Deshabilita el botón si solo hay una fila
                const disable = (container.children.length === 1);
                removeButtons.forEach(btn => btn.disabled = disable);
            }

            addButton.addEventListener('click', () => addDetailRow(null));

            container.addEventListener('click', function(e) {
                if (e.target.closest('.remove-detail-btn')) {
                    const rowToRemove = e.target.closest('.detalle-row');
                    if (container.children.length > 1) { // Asegura que siempre quede al menos una fila
                        rowToRemove.remove();
                        updateRemoveButtons();
                    }
                }
            });
            
            // Si hay errores de validación, mostrar el modal
            @if($errors->any())
                const crearModalEl = document.getElementById('crearModal');
                if (crearModalEl) {
                    setTimeout(() => {
                        const modal = new bootstrap.Modal(crearModalEl);
                        modal.show();
                    }, 100);
                }
            @endif
        });
    </script>
@endpush