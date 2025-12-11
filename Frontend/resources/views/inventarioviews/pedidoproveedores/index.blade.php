{{-- resources/views/inventarioviews/pedidoproveedores/index.blade.php (Refactorizado) --}}

@extends('layouts.app') 

@section('title', 'Gestión de Pedidos a Proveedores - El Castillo del Pan')

@push('styles')
    {{-- Estilos necesarios para la integración con el layout de dashboard --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    {{-- Asegúrate de que este CSS esté disponible en public/css/ --}}
    {{-- <link rel="stylesheet" href="/pre-produccion/PHP Modulos/css/stylemoduloinv.css"> --}}
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        
        {{-- Side Bar: Incluye el sidebar del nuevo layout --}}
        @include('components.admin-sidebar') 
        
        {{-- CONTENIDO PRINCIPAL --}}
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
            
            {{-- Título y Volver al Módulo Inventario --}}
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2">Gestión de Pedidos a Proveedores</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                
                </div>
            </div>

            {{-- MENSAJES --}}
            @if (session('success'))
                <div class="alert alert-success my-3">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger my-3">{{ session('error') }}</div>
            @endif
            
            {{-- Botón Crear Pedido --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fas fa-plus"></i> Crear Nuevo Pedido
                </button>
                <a href="{{ route('pedidoproveedores.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-sync"></i> Recargar Listado
                </a>
            </div>
                    
            <section id="listado" class="mb-5">
                <h3 class="mb-3 text-secondary">Listado de Pedidos</h3>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle shadow-sm rounded-3">
                        <thead class="table-light">
                            <tr>
                                <th>ID Pedido</th>
                                <th># Pedido</th>
                                <th>ID Proveedor</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th style="width: 180px;">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($pedidos as $pedido)
                                <tr>
                                    <td>{{ $pedido['idPedidoProv'] }}</td>
                                    <td>{{ $pedido['numeroPedido'] }}</td>
                                    <td>{{ $pedido['idProveedor'] }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pedido['fechaPedido'])->format('Y-m-d') }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match ($pedido['estadoPedido']) {
                                                'PENDIENTE' => 'bg-warning text-dark',
                                                'COMPLETADO' => 'bg-success',
                                                'CANCELADO' => 'bg-danger',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $pedido['estadoPedido'] }}</span>
                                    </td>

                                    <td class="text-nowrap">
                                        {{-- BOTÓN VER DETALLE --}}
                                        <a href="{{ route('pedidoproveedores.show', $pedido['idPedidoProv']) }}" 
                                            class="btn btn-info btn-sm me-1" title="Ver Detalles">
                                            <i class="fas fa-eye"></i>
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
            <section>
                <div class="modal fade" id="crearModal" tabindex="-1">
                    <div class="modal-dialog modal-xl"> {{-- Modal más grande para contener los detalles --}}
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">Crear Nuevo Pedido de Proveedor</h5>
                                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <form method="POST" action="{{ route('pedidoproveedores.store') }}" class="p-4">
                                @csrf

                                {{-- Encabezado del Pedido --}}
                                <h4 class="mb-3 text-primary"><i class="fas fa-file-invoice"></i> Encabezado del Pedido</h4>
                                <div class="row g-3 mb-4 border p-3 rounded-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">ID del Proveedor</label>
                                        <input type="number" name="idProveedor" class="form-control" required placeholder="Ej: 15">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Número de Pedido (Referencia)</label>
                                        <input type="number" name="numeroPedido" class="form-control" required placeholder="Ej: 2024001">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Estado Inicial</label>
                                        <select name="estadoPedido" class="form-select" required>
                                            <option value="PENDIENTE" selected>PENDIENTE</option>
                                            <option value="COMPLETADO">COMPLETADO</option>
                                            <option value="CANCELADO">CANCELADO</option>
                                        </select>
                                    </div>
                                </div>
                                
                                {{-- Detalles del Pedido --}}
                                <h4 class="mb-3 text-success"><i class="fas fa-list-ul"></i> Detalles del Pedido (Ingredientes)</h4>
                                <div id="detalles-container">
                                    {{-- Fila inicial, la que se clona con JS --}}
                                    <div class="row g-3 detalle-row mb-3 border p-3 bg-light rounded-3">
                                        <div class="col-md-4">
                                            <label class="form-label small">Ingrediente ID</label>
                                            <input type="number" name="detalles[0][idIngrediente]" class="form-control form-control-sm" required placeholder="ID Ingrediente">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small">Cantidad</label>
                                            <input type="number" name="detalles[0][cantidad]" class="form-control form-control-sm" required min="1" placeholder="Cantidad">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small">Precio Unitario</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">$</span>
                                                <input type="number" step="0.01" name="detalles[0][precioUnitario]" class="form-control" required min="0" placeholder="0.00">
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger w-100 btn-sm remove-detail-btn" disabled>
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12 text-end mt-3">
                                    <button type="button" id="add-detail-btn" class="btn btn-success btn-sm">
                                        <i class="fas fa-plus"></i> Agregar Ingrediente
                                    </button>
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-save"></i> Guardar Pedido Completo
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
    {{-- Mantenemos el script original para manejar los campos dinámicos --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let detailIndex = 1;
            const container = document.getElementById('detalles-container');
            const addButton = document.getElementById('add-detail-btn');

            function addDetailRow() {
                const newRow = document.createElement('div');
                newRow.className = 'row g-3 detalle-row mb-3 border p-3 bg-light rounded-3';
                newRow.innerHTML = `
                    <div class="col-md-4">
                        <label class="form-label small">Ingrediente ID</label>
                        <input type="number" name="detalles[${detailIndex}][idIngrediente]" class="form-control form-control-sm" required placeholder="ID Ingrediente">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Cantidad</label>
                        <input type="number" name="detalles[${detailIndex}][cantidad]" class="form-control form-control-sm" required min="1" placeholder="Cantidad">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Precio Unitario</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="detalles[${detailIndex}][precioUnitario]" class="form-control" required min="0" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger w-100 btn-sm remove-detail-btn">
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
                    // Opcional: Reindexar los campos si es necesario para el backend
                    // Esto se vuelve complejo; Laravel maneja arrays con índices no contiguos generalmente bien.
                }
            }
            
            function updateRemoveButtons() {
                const removeButtons = container.querySelectorAll('.remove-detail-btn');
                // Deshabilita el botón de eliminar si solo queda una fila
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
@endpush