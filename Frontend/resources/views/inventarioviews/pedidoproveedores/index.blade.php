{{-- resources/views/inventarioviews/pedidoproveedores/index.blade.php --}}

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Pedidos a Proveedores - El Castillo del Pan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    {{-- Asegúrate de incluir aquí tu CSS específico, como stylemoduloinv.css --}}
</head>

<body>
    <div class="container-fluid">
        <div class="row g-0">
            @include('partials.sidebar-inventario') {{-- Asegúrate de que el sidebar tenga el enlace activo --}}

            <div class="col-md-9 col-lg-10 main-content">

                {{-- NAVBAR SUPERIOR --}}
                    @include('partials.topbarinventario')

                <h1 class="mt-3">Gestión de Pedidos a Proveedores</h1>

                {{-- MENSAJES --}}
                @if (session('success'))
                    <div class="alert alert-success my-3">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger my-3">{{ session('error') }}</div>
                @endif
                
                {{-- MODAL para crear el Pedido (es complejo y denso) --}}
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fas fa-plus"></i> Crear Nuevo Pedido
                </button>
                    
                <section id="listado" class="mb-5">
                    <h2>Listado de Pedidos</h2>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID Pedido</th>
                                    <th># Pedido</th>
                                    <th>ID Proveedor</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($pedidos as $pedido)
                                    <tr>
                                        <td>{{ $pedido['idPedidoProv'] }}</td>
                                        <td>{{ $pedido['numeroPedido'] }}</td>
                                        <td>{{ $pedido['idProveedor'] }}</td>
                                        <td>{{ \Carbon\Carbon::parse($pedido['fechaPedido'])->format('Y-m-d') }}</td>
                                        <td><span class="badge bg-primary">{{ $pedido['estadoPedido'] }}</span></td>

                                        <td>
                                            {{-- BOTÓN VER DETALLE --}}
                                            <a href="{{ route('pedidoproveedores.show', $pedido['idPedidoProv']) }}" 
                                               class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> Detalles
                                            </a>

                                            {{-- BOTÓN ELIMINAR --}}
                                            <form method="POST"
                                                action="{{ route('pedidoproveedores.destroy', $pedido['idPedidoProv']) }}"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('¿Eliminar este pedido?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No hay pedidos registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- MODAL CREAR PEDIDO (Implementación avanzada con detalles) --}}
                {{-- Nota: La creación de un pedido con detalles es compleja y requiere JS. Aquí solo se pone la estructura básica del encabezado. --}}
                <div class="modal fade" id="crearModal" tabindex="-1">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content p-3">
                            <div class="modal-header">
                                <h5 class="modal-title">Crear Nuevo Pedido de Proveedor</h5>
                                <button class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <form method="POST" action="{{ route('pedidoproveedores.store') }}" class="row g-3 p-3">
                                @csrf

                                <h3>Encabezado del Pedido</h3>
                                <div class="col-md-4">
                                    <label class="form-label">Proveedor (ID)</label>
                                    <input type="number" name="idProveedor" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Número de Pedido</label>
                                    <input type="number" name="numeroPedido" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Estado</label>
                                    <select name="estadoPedido" class="form-select" required>
                                        <option value="PENDIENTE">PENDIENTE</option>
                                        <option value="COMPLETADO">COMPLETADO</option>
                                        <option value="CANCELADO">CANCELADO</option>
                                    </select>
                                </div>
                                
                                <hr>
                                
                                <h3>Detalles del Pedido (Ingredientes)</h3>
                                {{-- Esta sección requiere JavaScript para agregar dinámicamente filas de ingredientes --}}
                                <div id="detalles-container">
                                    <div class="row g-3 detalle-row mb-2">
                                        <div class="col-md-4">
                                            <label class="form-label">Ingrediente ID</label>
                                            <input type="number" name="detalles[0][idIngrediente]" class="form-control" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Cantidad</label>
                                            <input type="number" name="detalles[0][cantidad]" class="form-control" required min="1">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Precio Unitario</label>
                                            <input type="number" step="0.01" name="detalles[0][precioUnitario]" class="form-control" required min="0">
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger w-100 remove-detail-btn" disabled>
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12 text-end">
                                    <button type="button" id="add-detail-btn" class="btn btn-success">
                                        <i class="fas fa-plus"></i> Agregar Ingrediente
                                    </button>
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        Guardar Pedido Completo
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
            let detailIndex = 1;
            const container = document.getElementById('detalles-container');
            const addButton = document.getElementById('add-detail-btn');

            function addDetailRow() {
                const newRow = document.createElement('div');
                newRow.className = 'row g-3 detail-row mb-2';
                newRow.innerHTML = `
                    <div class="col-md-4">
                        <label class="form-label">Ingrediente ID</label>
                        <input type="number" name="detalles[${detailIndex}][idIngrediente]" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Cantidad</label>
                        <input type="number" name="detalles[${detailIndex}][cantidad]" class="form-control" required min="1">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Precio Unitario</label>
                        <input type="number" step="0.01" name="detalles[${detailIndex}][precioUnitario]" class="form-control" required min="0">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger w-100 remove-detail-btn">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                `;
                container.appendChild(newRow);
                detailIndex++;
                updateRemoveButtons();
            }

            function removeDetailRow(button) {
                // Solo elimina si hay más de una fila
                if (container.children.length > 1) {
                    button.closest('.detail-row').remove();
                    updateRemoveButtons();
                }
            }
            
            function updateRemoveButtons() {
                const removeButtons = container.querySelectorAll('.remove-detail-btn');
                removeButtons.forEach(btn => btn.disabled = (container.children.length === 1));
            }

            addButton.addEventListener('click', addDetailRow);
            container.addEventListener('click', function(e) {
                if (e.target.closest('.remove-detail-btn')) {
                    removeDetailRow(e.target.closest('.remove-detail-btn'));
                }
            });
            
            updateRemoveButtons(); // Inicializar el estado de los botones
        });
    </script>
</body>
</html>