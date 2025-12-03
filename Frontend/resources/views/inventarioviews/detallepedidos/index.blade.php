@extends('layouts.app')

@section('title', 'Gestión de Detalles de Pedidos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/stylemoduloinv.css') }}">
@endpush

@section('content')

<div class="container-fluid">
    <div class="row g-0">

        {{-- Sidebar --}}
        @include('partials.sidebar-inventario')

        <div class="col-md-9 col-lg-10 main-content">

            <h1 class="mb-3">Gestión de Detalles de Pedidos</h1>
            <p class="lead">Administra los detalles de los pedidos registrados en el sistema.</p>

            {{-- ALERTAS --}}
            @if (session('status'))
                <div class="alert alert-{{ session('status_type') }} mt-3">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger mt-3">
                    {{ session('error') }}
                </div>
            @endif

            {{-- BOTÓN CREAR --}}
            <button class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#modalCrear">
                <i class="fas fa-plus"></i> Nuevo Detalle de Pedido
            </button>

            {{-- TABLA --}}
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID Detalle</th>
                            <th>ID Pedido</th>
                            <th>ID Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                            <th style="width: 180px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($detalles as $d)
                        <tr>
                            <td>{{ $d['idDetalle'] ?? $d['ID_DETALLE'] }}</td>
                            <td>{{ $d['idPedido'] ?? $d['ID_PEDIDO'] }}</td>
                            <td>{{ $d['idProducto'] ?? $d['ID_PRODUCTO'] }}</td>
                            <td>{{ $d['cantidadProducto'] ?? $d['CANTIDAD_PRODUCTO'] }}</td>
                            <td>{{ $d['precioUnitario'] ?? $d['PRECIO_UNITARIO'] }}</td>
                            <td>{{ $d['subtotal'] ?? $d['SUBTOTAL'] }}</td>

                            <td>
                                {{-- Botón Editar --}}
                                <button
                                    class="btn btn-warning btn-sm btnEditar"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditar"
                                    data-id="{{ $d['idDetalle'] ?? $d['ID_DETALLE'] }}"
                                    data-pedido="{{ $d['idPedido'] ?? $d['ID_PEDIDO'] }}"
                                    data-producto="{{ $d['idProducto'] ?? $d['ID_PRODUCTO'] }}"
                                    data-cantidad="{{ $d['cantidadProducto'] ?? $d['CANTIDAD_PRODUCTO'] }}"
                                    data-precio="{{ $d['precioUnitario'] ?? $d['PRECIO_UNITARIO'] }}"
                                    data-subtotal="{{ $d['subtotal'] ?? $d['SUBTOTAL'] }}"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>

                                {{-- Formulario Eliminar --}}
                                <form action="{{ route('detallePedidos.destroy', ['id' => $d['idDetalle'] ?? $d['ID_DETALLE']]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm"
                                        onclick="return confirm('¿Eliminar este detalle de pedido?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No hay detalles de pedidos registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ========================================================= --}}
            {{--   MODAL CREAR DETALLE PEDIDO                              --}}
            {{-- ========================================================= --}}
            <div class="modal fade" id="modalCrear" tabindex="-1">
                <div class="modal-dialog">
                    <form class="modal-content" method="POST" action="{{ route('detallePedidos.store') }}">
                        @csrf
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Nuevo Detalle de Pedido</h5>
                            <button class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">

                            <label class="form-label">ID Pedido:</label>
                            <input type="number" name="idPedido" class="form-control" required>

                            <label class="form-label">ID Producto:</label>
                            <input type="number" name="idProducto" class="form-control" required>

                            <label class="form-label">Cantidad:</label>
                            <input type="number" name="cantidadProducto" class="form-control" required>

                            <label class="form-label">Precio Unitario:</label>
                            <input type="number" step="0.01" name="precioUnitario" class="form-control" required>

                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ========================================================= --}}
            {{--   MODAL EDITAR DETALLE PEDIDO                              --}}
            {{-- ========================================================= --}}
            <div class="modal fade" id="modalEditar" tabindex="-1">
                <div class="modal-dialog">
                    <form class="modal-content" method="POST" action="{{ route('detallePedidos.update', ['id' => 0]) }}">
                        @csrf
                        @method('PUT')

                        <div class="modal-header bg-warning">
                            <h5 class="modal-title">Editar Detalle de Pedido</h5>
                            <button class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <input type="hidden" name="idDetalle" id="editId">

                            <label class="form-label">ID Pedido:</label>
                            <input type="number" name="idPedido" id="editPedido" class="form-control" required>

                            <label class="form-label">ID Producto:</label>
                            <input type="number" name="idProducto" id="editProducto" class="form-control" required>

                            <label class="form-label">Cantidad:</label>
                            <input type="number" name="cantidadProducto" id="editCantidad" class="form-control" required>

                            <label class="form-label">Precio Unitario:</label>
                            <input type="number" step="0.01" name="precioUnitario" id="editPrecio" class="form-control" required>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button class="btn btn-warning">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Script para cargar datos en el modal de editar --}}
<script>
document.addEventListener("DOMContentLoaded", () => {
    const modalEditar = document.getElementById("modalEditar");

    modalEditar.addEventListener("show.bs.modal", function (event) {
        const button = event.relatedTarget;

        const id = button.getAttribute("data-id");
        const pedido = button.getAttribute("data-pedido");
        const producto = button.getAttribute("data-producto");
        const cantidad = button.getAttribute("data-cantidad");
        const precio = button.getAttribute("data-precio");

        document.getElementById("editId").value = id;
        document.getElementById("editPedido").value = pedido;
        document.getElementById("editProducto").value = producto;
        document.getElementById("editCantidad").value = cantidad;
        document.getElementById("editPrecio").value = precio;

        // Actualizar action del formulario
        modalEditar.querySelector("form").action = `/dashboard/inventario/detalle-pedidos/update/${id}`;
    });
});
</script>

@endsection
