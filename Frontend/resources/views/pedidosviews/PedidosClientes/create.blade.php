@extends('layouts.app')

@section('title', 'Crear Pedido')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .btn-quitar { border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; padding: 0; }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        @php $esAdmin = Auth::check() && Auth::user()->rol === 'admin'; @endphp
        @if ($esAdmin) @include('components.admin-sidebar') @else @include('components.employee-sidebar') @endif
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2">Crear Nuevo Pedido</h1>
            </div>

            <section class="p-4 bg-white shadow-sm rounded-3">
                <form action="{{ route('pedidos.store') }}" method="POST" id="pedidoForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="ID_CLIENTE" class="form-label fw-bold">ID Cliente</label>
                            <input type="number" class="form-control" id="ID_CLIENTE" name="ID_CLIENTE" value="{{ old('ID_CLIENTE') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="ID_EMPLEADO" class="form-label fw-bold">ID Empleado</label>
                            <input type="number" class="form-control" id="ID_EMPLEADO" name="ID_EMPLEADO" value="{{ old('ID_EMPLEADO', Auth::user()->id_empleado ?? '') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="ID_ESTADO_PEDIDO" class="form-label fw-bold">Estado (ID)</label>
                            <input type="number" class="form-control" id="ID_ESTADO_PEDIDO" name="ID_ESTADO_PEDIDO" value="{{ old('ID_ESTADO_PEDIDO', 1) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="FECHA_ENTREGA" class="form-label fw-bold">Fecha de Entrega</label>
                            <input type="date" class="form-control" id="FECHA_ENTREGA" name="FECHA_ENTREGA" value="{{ old('FECHA_ENTREGA') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="TOTAL_PRODUCTO" class="form-label fw-bold">Total General ($)</label>
                            <input type="number" step="0.01" class="form-control bg-light" id="TOTAL_PRODUCTO" name="TOTAL_PRODUCTO" value="{{ old('TOTAL_PRODUCTO', 0) }}" readonly>
                        </div>

                        {{-- SECCIÓN DE PRODUCTOS --}}
                        <div class="col-12 mt-4">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                <h5 class="mb-0"><i class="fas fa-shopping-basket me-2"></i>Productos del Pedido</h5>
                                <button type="button" class="btn btn-sm btn-primary" id="btn-agregar-fila">
                                    <i class="fas fa-plus"></i> Añadir Producto
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Producto</th>
                                            <th width="150">Cantidad</th>
                                            <th width="200">Precio Unit.</th>
                                            <th width="200">Subtotal</th>
                                            <th width="50"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="detalles-pedido-body">
                                        {{-- Las filas se generan con JS --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar Pedido</button>
                            <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </div>
                </form>
            </section>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let listaProductosGlobal = [];

    async function cargarProductos() {
        try {
            const response = await fetch("{{ route('api.productos.lista') }}");
            const data = await response.json();
            listaProductosGlobal = data.map(p => ({
                id: p.id_PRODUCTO || p.idProducto || p["Id Producto:"],
                nombre: p.nombre_PRODUCTO || p.nombreProducto || p["Nombre Producto:"],
                precio: p.precio_UNITARIO || p.precio || p["Precio:"] || 0
            }));
        } catch (error) { console.error("Error cargando productos:", error); }
    }

    document.addEventListener('DOMContentLoaded', async () => {
        await cargarProductos();
        const detallesBody = document.getElementById('detalles-pedido-body');

        function agregarFila() {
            let opciones = listaProductosGlobal.map(p => `<option value="${p.id}" data-precio="${p.precio}">${p.nombre}</option>`).join('');
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td><select name="productos[]" class="form-select select-producto" required><option value="">Seleccione...</option>${opciones}</select></td>
                <td><input type="number" name="cantidades[]" class="form-control text-center input-cantidad" value="1" min="1" required></td>
                <td><input type="number" name="precios_unitarios[]" class="form-control bg-light input-precio" readonly value="0"></td>
                <td><input type="number" name="subtotales[]" class="form-control bg-light input-subtotal" readonly value="0"></td>
                <td><button type="button" class="btn btn-danger btn-sm btn-quitar"><i class="fas fa-times"></i></button></td>`;
            detallesBody.appendChild(fila);
        }

        document.getElementById('btn-agregar-fila').onclick = agregarFila;

        detallesBody.addEventListener('input', (e) => {
            const fila = e.target.closest('tr');
            const select = fila.querySelector('.select-producto');
            const cant = fila.querySelector('.input-cantidad').value || 0;
            const precio = select.options[select.selectedIndex]?.dataset.precio || 0;
            
            fila.querySelector('.input-precio').value = precio;
            fila.querySelector('.input-subtotal').value = (precio * cant).toFixed(2);
            
            let total = 0;
            document.querySelectorAll('.input-subtotal').forEach(i => total += parseFloat(i.value) || 0);
            document.getElementById('TOTAL_PRODUCTO').value = total.toFixed(2);
        });

        detallesBody.addEventListener('click', (e) => {
            if (e.target.closest('.btn-quitar')) {
                e.target.closest('tr').remove();
                detallesBody.dispatchEvent(new Event('input', {bubbles: true}));
            }
        });

        agregarFila(); // Agregar una fila por defecto
    });
</script>
@endpush
