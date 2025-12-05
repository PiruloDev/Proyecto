{{-- resources/views/inventarioviews/recetas/show.blade.php --}}

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de Receta - Producto #{{ $idProducto }}</title>
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

                <h1 class="mt-3">Receta para Producto ID <span class="text-primary">{{ $idProducto }}</span></h1>

                <div class="mb-3 d-flex justify-content-between">
                    <a href="{{ route('recetas.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver al Listado
                    </a>
                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editarModal">
                        <i class="fas fa-edit"></i> Editar Receta
                    </button>
                </div>

                {{-- LISTA DE DETALLES DE LA RECETA --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-list"></i> Ingredientes y Cantidades Requeridas
                    </div>
                    <div class="card-body p-0">
                        @if (empty($detalles))
                            <div class="alert alert-info m-3">Esta receta no tiene ingredientes definidos.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID Ingrediente</th>
                                            <th>Cantidad Requerida</th>
                                            <th>ID Unidad</th>
                                            <th>ID Detalle Receta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($detalles as $detalle)
                                            <tr>
                                                <td>{{ $detalle['idIngrediente'] }}</td>
                                                {{-- Usamos number_format para precisión en el front-end --}}
                                                <td><strong class="text-success">{{ number_format($detalle['cantidadRequerida'], 3) }}</strong></td>
                                                <td>{{ $detalle['idUnidad'] }}</td>
                                                <td>{{ $detalle['idReceta'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- MODAL EDITAR RECETA (Utiliza la misma lógica de campos dinámicos que el modal de creación) --}}
    <div class="modal fade" id="editarModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content p-4">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Receta para Producto ID {{ $idProducto }}</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                {{-- NOTA: El verbo HTTP para actualizar es PUT, se usa @method('PUT') --}}
                <form method="POST" action="{{ route('recetas.update', $idProducto) }}" class="row g-3 p-3">
                    @csrf
                    @method('PUT')
                    
                    <p class="text-danger">Advertencia: La actualización reemplazará TODOS los detalles de la receta por los que se definan a continuación.</p>
                    
                    <h4>Ingredientes Requeridos</h4>
                    
                    <div id="edit-detalles-container">
                        {{-- Esto se llenará con JavaScript al abrir el modal --}}
                    </div>
                    
                    <div class="col-12 text-end">
                        <button type="button" id="edit-add-detail-btn" class="btn btn-sm btn-success">
                            <i class="fas fa-plus"></i> Agregar Ingrediente
                        </button>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-warning w-100">
                            Actualizar Receta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- SCRIPT PARA MANEJAR CAMPOS DINÁMICOS DE RECETA EN EL MODAL DE EDICIÓN --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('editarModal');
            const container = document.getElementById('edit-detalles-container');
            const addButton = document.getElementById('edit-add-detail-btn');
            const initialDetalles = @json($detalles); // Datos actuales de la receta
            
            let detailIndex = 0;
            
            function addDetailRow(index, data = null) {
                const currentIndex = index !== null ? index : detailIndex;
                
                const newRow = document.createElement('div');
                newRow.className = 'row g-3 detalle-row mb-2';
                newRow.setAttribute('data-index', currentIndex);

                // Usamos los nombres de campo que espera el DTO RecetaRequest: idIngrediente, cantidadNecesaria, idUnidad
                const idIngredienteVal = data ? (data.idIngrediente || '') : '';
                const cantidadVal = data ? (data.cantidadRequerida || '') : ''; // Ojo: en la DB es 'cantidadRequerida'
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
                removeButtons.forEach(btn => btn.disabled = (container.children.length === 1));
            }

            function clearAndLoadDetails(detalles) {
                container.innerHTML = '';
                detailIndex = 0;

                if (detalles.length === 0) {
                    addDetailRow(null); // Agrega una fila vacía si no hay detalles
                } else {
                    detalles.forEach((detalle) => {
                        addDetailRow(null, detalle);
                    });
                }
            }

            // 1. Cargar detalles al abrir el modal
            modal.addEventListener('show.bs.modal', function () {
                // Si la edición falló antes, usa los datos viejos. Si no, usa los datos actuales de la receta.
                const oldInput = @json(session()->getOldInput());
                const dataToLoad = oldInput.ingredientes ? oldInput.ingredientes.map(i => ({
                    idIngrediente: i.idIngrediente,
                    cantidadRequerida: i.cantidadNecesaria, // Mapeo de vuelta para la función de carga
                    idUnidad: i.idUnidad
                })) : initialDetalles;
                
                clearAndLoadDetails(dataToLoad);
            });

            // 2. Agregar nueva fila
            addButton.addEventListener('click', () => addDetailRow(null));

            // 3. Eliminar fila
            container.addEventListener('click', function(e) {
                if (e.target.closest('.remove-detail-btn')) {
                    const rowToRemove = e.target.closest('.detalle-row');
                    if (container.children.length > 1) { 
                        rowToRemove.remove();
                        updateRemoveButtons();
                    }
                }
            });
            
            // Si hubo error de validación en la actualización, mostrar el modal automáticamente
            @if($errors->any() && Route::currentRouteName() == 'recetas.update')
                const editModal = new bootstrap.Modal(modal);
                editModal.show();
            @endif
        });
    </script>
</body>
</html>