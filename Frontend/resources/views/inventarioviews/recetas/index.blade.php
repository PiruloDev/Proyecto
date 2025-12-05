{{-- resources/views/inventarioviews/recetas/index.blade.php --}}

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Recetas - El Castillo del Pan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    {{-- Incluye tus estilos aquí --}}
</head>

<body>
    <div class="container-fluid">
        <div class="row g-0">
            @include('partials.sidebar-inventario')

            <div class="col-md-9 col-lg-10 main-content">

                <h1 class="mt-3">Gestión de Recetas</h1>

                {{-- MENSAJES --}}
                @if (session('success'))
                    <div class="alert alert-success my-3">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger my-3">{{ session('error') }}</div>
                @endif
                
                {{-- Botón para abrir el modal de creación --}}
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fas fa-plus"></i> Crear Nueva Receta
                </button>
                    
                <section id="listado-recetas" class="mb-5">
                    <h2>Recetas por Producto</h2>

                    @forelse($recetas as $receta)
                        <div class="card shadow-sm mb-3">
                            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                                <h5>
                                    <i class="fas fa-utensils"></i> Receta para Producto: 
                                    <strong>{{ $receta['nombreProducto'] }}</strong> (ID: {{ $receta['idProducto'] }})
                                </h5>
                                <div>
                                    {{-- Botón VER DETALLE (te lleva a show.blade.php) --}}
                                    <a href="{{ route('recetas.show', $receta['idProducto']) }}" 
                                       class="btn btn-sm btn-info me-2">
                                        <i class="fas fa-eye"></i> Ver Detalles
                                    </a>
                                    
                                    {{-- Botón ELIMINAR --}}
                                    <form method="POST"
                                        action="{{ route('recetas.destroy', $receta['idProducto']) }}"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('ADVERTENCIA: ¿Eliminar la receta del Producto ID {{ $receta['idProducto'] }}?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><strong>Ingredientes (Detalles):</strong></p>
                                <ul class="list-group list-group-flush">
                                    @foreach($receta['detalles'] as $detalle)
                                        <li class="list-group-item d-flex justify-content-between align-items-center py-1">
                                            <span>
                                                **{{ number_format($detalle['cantidadRequerida'], 3) }}** (Unidad ID: {{ $detalle['idUnidad'] }}) de Ingrediente ID: {{ $detalle['idIngrediente'] }}
                                            </span>
                                            <small class="text-muted">ID Receta Detalle: {{ $detalle['idReceta'] }}</small>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-warning">No hay recetas registradas.</div>
                    @endforelse
                </section>

                {{-- MODAL CREAR RECETA --}}
                <div class="modal fade" id="crearModal" tabindex="-1">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content p-4">
                            <div class="modal-header">
                                <h5 class="modal-title">Crear Nueva Receta</h5>
                                <button class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <form method="POST" action="{{ route('recetas.store') }}" class="row g-3 p-3">
                                @csrf

                                <h3>Producto y Detalles</h3>
                                <div class="col-12 mb-3">
                                    <label for="idProducto" class="form-label">ID Producto Terminado (Único)</label>
                                    <input type="number" id="idProducto" name="idProducto" class="form-control" required value="{{ old('idProducto') }}">
                                    @error('idProducto')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                                
                                <hr>
                                
                                <h4>Ingredientes Requeridos</h4>
                                <p class="text-muted">Defina la lista completa de ingredientes para esta receta.</p>
                                
                                <div id="detalles-container">
                                    {{-- El script JS llenará esto o se llenará si hay errores de validación --}}
                                </div>
                                
                                <div class="col-12 text-end">
                                    <button type="button" id="add-detail-btn" class="btn btn-sm btn-success">
                                        <i class="fas fa-plus"></i> Agregar Ingrediente a Receta
                                    </button>
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        Guardar Receta
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- SCRIPT PARA MANEJAR CAMPOS DINÁMICOS DE RECETA --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('detalles-container');
            const addButton = document.getElementById('add-detail-btn');
            
            // Lógica para manejar índices y datos persistentes en caso de error
            let detailIndex = 0;
            const oldDetalles = @json(old('ingredientes', []));
            
            if (oldDetalles.length > 0) {
                // Si hay datos viejos, cargarlos
                oldDetalles.forEach((detalle, index) => {
                    addDetailRow(index, detalle);
                });
                detailIndex = oldDetalles.length;
            } else {
                // Si no hay, agregar una fila inicial
                addDetailRow(0, null);
                detailIndex = 1;
            }
            
            function addDetailRow(index, data = null) {
                const currentIndex = index !== null ? index : detailIndex;
                
                const newRow = document.createElement('div');
                newRow.className = 'row g-3 detalle-row mb-2';
                newRow.setAttribute('data-index', currentIndex);

                const idIngredienteVal = data ? (data.idIngrediente || '') : '';
                const cantidadVal = data ? (data.cantidadNecesaria || '') : '';
                const idUnidadVal = data ? (data.idUnidad || '') : '';

                newRow.innerHTML = `
                    <div class="col-md-4">
                        <label class="form-label">ID Ingrediente</label>
                        <input type="number" name="ingredientes[${currentIndex}][idIngrediente]" class="form-control" required value="${idIngredienteVal}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cantidad Necesaria</label>
                        <input type="number" step="0.001" name="ingredientes[${currentIndex}][cantidadNecesaria]" class="form-control" required min="0.001" value="${cantidadVal}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">ID Unidad</label>
                        <input type="number" name="ingredientes[${currentIndex}][idUnidad]" class="form-control" required value="${idUnidadVal}">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger w-100 remove-detail-btn" ${container.children.length === 0 ? 'disabled' : ''}>
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
                removeButtons.forEach(btn => btn.disabled = (container.children.length === 1));
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
            
            // Si hay errores de validación, el modal se debe mostrar
            @if($errors->any())
                const modal = new bootstrap.Modal(document.getElementById('crearModal'));
                modal.show();
            @endif
        });
    </script>
</body>
</html>