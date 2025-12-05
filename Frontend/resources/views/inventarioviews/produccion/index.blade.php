{{-- resources/views/inventarioviews/produccion/index.blade.php --}}

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Producción - El Castillo del Pan</title>
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

                <h1 class="mt-3">Gestión de Producción</h1>

                {{-- MENSAJES --}}
                @if (session('success'))
                    <div class="alert alert-success my-3">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger my-3">{{ session('error') }}</div>
                @endif
                
                {{-- Botón para abrir el modal de registro --}}
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#registrarModal">
                    <i class="fas fa-plus"></i> Registrar Nueva Producción
                </button>
                    
                <section id="historial" class="mb-5">
                    <h2>Historial de Producción</h2>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID Producción</th>
                                    <th>ID Producto</th>
                                    <th>Cantidad Producida</th>
                                    <th>Fecha y Hora</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($historial as $registro)
                                    <tr>
                                        <td>{{ $registro['idProduccion'] }}</td>
                                        <td>{{ $registro['idProducto'] }}</td>
                                        {{-- El number_format es clave para mostrar BigDecimal correctamente --}}
                                        <td>{{ number_format($registro['cantidadProducida'], 2) }}</td> 
                                        <td>
                                            @if(isset($registro['fechaProduccion']))
                                                {{-- Se asume formato ISO 8601 o similar que Carbon puede parsear --}}
                                                {{ \Carbon\Carbon::parse($registro['fechaProduccion'])->format('Y-m-d H:i:s') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>

                                        <td>
                                            {{-- BOTÓN ELIMINAR (Reversión de Inventario) --}}
                                            <form method="POST"
                                                action="{{ route('produccion.destroy', $registro['idProduccion']) }}"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('ADVERTENCIA: ¿Eliminar y REVERTIR los cambios de inventario (ingredientes y stock)?')">
                                                    <i class="fas fa-undo"></i> Revertir / Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No hay registros de producción.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- MODAL REGISTRAR PRODUCCIÓN --}}
                <div class="modal fade" id="registrarModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content p-3">
                            <div class="modal-header">
                                <h5 class="modal-title">Registrar Nueva Producción</h5>
                                <button class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <form method="POST" action="{{ route('produccion.store') }}" class="row g-3 p-3">
                                @csrf

                                <h3>Detalles de la Producción</h3>
                                <div class="col-md-6">
                                    <label for="idProducto" class="form-label">ID Producto Terminado</label>
                                    <input type="number" id="idProducto" name="idProducto" class="form-control" required value="{{ old('idProducto') }}">
                                    @error('idProducto')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="cantidadProducida" class="form-label">Cantidad Producida</label>
                                    <input type="number" step="0.01" id="cantidadProducida" name="cantidadProducida" class="form-control" required min="0.01" value="{{ old('cantidadProducida') }}">
                                    @error('cantidadProducida')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                                
                                <hr>
                                
                                <h4>Descuento Manual de Ingredientes (Opcional)</h4>
                                <p class="text-muted">Solo si la producción no sigue la receta estándar o se necesita un ajuste manual.</p>
                                
                                <div id="detalles-container">
                                    {{-- Fila inicial de detalle (oculta para JS o solo visible si old('ingredientesDescontados') no es null) --}}
                                    @if(old('ingredientesDescontados'))
                                        @foreach(old('ingredientesDescontados') as $index => $detalle)
                                            <div class="row g-3 detalle-row mb-2" data-index="{{ $index }}">
                                                <div class="col-md-5">
                                                    <label class="form-label">ID Ingrediente</label>
                                                    <input type="number" name="ingredientesDescontados[{{ $index }}][idIngrediente]" class="form-control" required value="{{ $detalle['idIngrediente'] ?? '' }}">
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label">Cantidad Usada</label>
                                                    <input type="number" step="0.01" name="ingredientesDescontados[{{ $index }}][cantidadUsada]" class="form-control" required min="0.01" value="{{ $detalle['cantidadUsada'] ?? '' }}">
                                                </div>
                                                <div class="col-md-2 d-flex align-items-end">
                                                    <button type="button" class="btn btn-danger w-100 remove-detail-btn">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                
                                <div class="col-12 text-end">
                                    <button type="button" id="add-detail-btn" class="btn btn-sm btn-success">
                                        <i class="fas fa-plus"></i> Agregar Ingrediente Manual
                                    </button>
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        Registrar Producción
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
    
    {{-- SCRIPT PARA MANEJAR CAMPOS DINÁMICOS DEL DETALLE --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('detalles-container');
            const addButton = document.getElementById('add-detail-btn');
            
            // Determinar el índice inicial basado en los elementos existentes (si hay errores previos)
            let detailIndex = container.querySelectorAll('.detalle-row').length;
            if (detailIndex === 0) {
                detailIndex = 0; // Si no hay elementos previos, empezamos en 0
            } else {
                // Si hay elementos previos, encontramos el índice más alto y sumamos 1
                const rows = container.querySelectorAll('.detalle-row');
                const lastIndex = parseInt(rows[rows.length - 1].dataset.index);
                detailIndex = lastIndex + 1;
            }


            function addDetailRow() {
                const newRow = document.createElement('div');
                newRow.className = 'row g-3 detalle-row mb-2';
                newRow.setAttribute('data-index', detailIndex);

                newRow.innerHTML = `
                    <div class="col-md-5">
                        <label class="form-label">ID Ingrediente</label>
                        <input type="number" name="ingredientesDescontados[${detailIndex}][idIngrediente]" class="form-control" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Cantidad Usada</label>
                        <input type="number" step="0.01" name="ingredientesDescontados[${detailIndex}][cantidadUsada]" class="form-control" required min="0.01">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger w-100 remove-detail-btn">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                `;
                container.appendChild(newRow);
                detailIndex++;
            }

            // Listener para agregar filas
            addButton.addEventListener('click', addDetailRow);

            // Listener para eliminar filas
            container.addEventListener('click', function(e) {
                if (e.target.closest('.remove-detail-btn')) {
                    const rowToRemove = e.target.closest('.detalle-row');
                    if (container.children.length > 0) {
                        rowToRemove.remove();
                    }
                }
            });
            
            // Mostrar modal si hubo errores de validación (para que los datos antiguos persistan)
            @if($errors->any())
                const modal = new bootstrap.Modal(document.getElementById('registrarModal'));
                modal.show();
            @endif
        });
    </script>
</body>
</html>