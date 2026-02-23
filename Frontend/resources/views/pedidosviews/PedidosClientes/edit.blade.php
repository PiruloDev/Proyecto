@extends('layouts.app')

@section('title', 'Editar Pedido - Portal Empleado')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .main-content { background-color: #fdfaf6; min-height: 100vh; overflow-y: auto; }
        .card { border-radius: 15px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .btn-quitar { border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; padding: 0; }
        /* Ajuste para el color temático del Portal Empleado (Marrón/Dorado) */
        .btn-update { background-color: #ffc107; border: none; font-weight: bold; color: #212529; }
        .btn-update:hover { background-color: #e0a800; }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        {{-- Sidebar del Empleado --}}
        @include('components.employee-sidebar') 
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4 main-content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2">Editar Pedido #{{ $pedido['id_PEDIDO'] }}</h1>
                <a href="{{ route('pedidos.index') }}" class="btn btn-outline-secondary shadow-sm">
                    <i class="fas fa-arrow-left"></i> Volver al Listado
                </a>
            </div>

            <form action="{{ route('pedidos.update', $pedido['id_PEDIDO']) }}" method="POST" id="editPedidoForm">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="card p-4">
                            <h5 class="text-secondary border-bottom pb-2 mb-3">Información del Pedido</h5>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">ID Cliente</label>
                                <input type="number" class="form-control" name="ID_CLIENTE" value="{{ $pedido['id_CLIENTE'] }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">ID Empleado</label>
                                <input type="number" class="form-control" name="ID_EMPLEADO" value="{{ $pedido['id_EMPLEADO'] }}" required readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Estado del Pedido</label>
                                <input type="number" class="form-control" name="ID_ESTADO_PEDIDO" value="{{ $pedido['id_ESTADO_PEDIDO'] }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Fecha de Entrega</label>
                                <input type="date" class="form-control" name="FECHA_ENTREGA" 
                                       value="{{ isset($pedido['fecha_ENTREGA']) ? substr($pedido['fecha_ENTREGA'], 0, 10) : '' }}">
                            </div>

                            <div class="mt-4 p-3 bg-light rounded text-center border">
                                <label class="form-label fw-bold d-block text-uppercase small text-muted">Total a Pagar</label>
                                <h3 class="text-success fw-bold mb-0">$<span id="total-general-span">{{ number_format($pedido['total_PRODUCTO'], 0, ',', '.') }}</span></h3>
                                <input type="hidden" name="TOTAL_PRODUCTO" id="TOTAL_PRODUCTO_INPUT" value="{{ $pedido['total_PRODUCTO'] }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="text-secondary mb-0"><i class="fas fa-shopping-cart me-2"></i>Productos Seleccionados</h5>
                                <button type="button" class="btn btn-sm btn-success" id="btn-agregar-fila">
                                    <i class="fas fa-plus"></i> Añadir Producto
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Producto</th>
                                            <th width="120">Cantidad</th>
                                            <th>Precio</th>
                                            <th>Subtotal</th>
                                            <th width="50"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="detalles-body">
                                        {{-- Sincronizado por JS --}}
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                                <button type="submit" class="btn btn-update btn-lg px-5 shadow-sm">
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let listaProductosGlobal = [];
    const detallesBody = document.getElementById('detalles-body');
    const totalSpan = document.getElementById('total-general-span');
    const totalInput = document.getElementById('TOTAL_PRODUCTO_INPUT');

    // 1. CARGAR PRODUCTOS Y LUEGO RENDERIZAR
    async function inicializarVista() {
        try {
            // Llamamos a la API de Java
            const response = await fetch('http://localhost:8080/productos');
            const data = await response.json();
            
            listaProductosGlobal = data.map(p => ({
                id: p.id_PRODUCTO || p.idProducto || p["Id Producto:"],
                nombre: p.nombre_PRODUCTO || p.nombreProducto || p["Nombre Producto:"],
                precio: parseFloat(p.precio_UNITARIO || p.precio || p["Precio:"] || 0)
            }));

            console.log("Productos cargados:", listaProductosGlobal);
            
            // SOLO cuando los productos ya están en la variable global, renderizamos
            renderizarDetallesExistentes();
        } catch (e) { 
            console.error("Error al cargar productos:", e); 
            alert("No se pudieron cargar los productos de la base de datos.");
        }
    }

    function renderizarDetallesExistentes() {
    // 1. Capturamos el objeto completo para debug
    const pedidoCompleto = @json($pedido);
    console.log("Datos del pedido recibidos:", pedidoCompleto);

    // 2. Buscamos detalles probando todas las combinaciones posibles de nombres de clave
    const detalles = pedidoCompleto.detalles || 
                     pedidoCompleto.detalle_pedidos || 
                     pedidoCompleto.detallePedidos || 
                     pedidoCompleto.detalles_pedido || [];
    
    detallesBody.innerHTML = '';

    if (detalles && detalles.length > 0) {
        detalles.forEach(d => {
            // Solo agregamos la fila si tiene un ID de producto válido
            const idProd = d.id_PRODUCTO || d.idProducto || d.id_producto;
            if (idProd) {
                agregarFila(d);
            }
        });
    } else {
        // Si es un pedido nuevo o no tiene items, una fila vacía por defecto
        agregarFila();
    }
    actualizarCalculos();
}

  function agregarFila(detalle = null) {
    // Soporte para id_PRODUCTO, idProducto, id_producto
    const prodId = detalle ? (detalle.id_PRODUCTO || detalle.idProducto || detalle.id_producto) : '';
    // Soporte para cantidad_PRODUCTO, cantidad, cantidadProducto
    const cant = detalle ? (detalle.cantidad_PRODUCTO || detalle.cantidad || detalle.cantidadProducto || 1) : 1;
        
        // Buscamos el precio actual en nuestra lista global para que sea exacto
        const productoEncontrado = listaProductosGlobal.find(p => p.id == prodId);
        const precio = productoEncontrado ? productoEncontrado.precio : 0;
        const subtotal = precio * cant;

        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td>
                <select name="productos[]" class="form-select select-producto" required>
                    <option value="">Seleccione...</option>
                    ${listaProductosGlobal.map(p => `
                        <option value="${p.id}" data-precio="${p.precio}" ${p.id == prodId ? 'selected' : ''}>
                            ${p.nombre}
                        </option>`).join('')}
                </select>
            </td>
            <td>
                <input type="number" name="cantidades[]" class="form-control input-cantidad" value="${cant}" min="1">
            </td>
            <td>$<span class="txt-precio">${precio.toLocaleString('es-CO')}</span></td>
            <td class="fw-bold text-dark">$<span class="txt-subtotal">${subtotal.toLocaleString('es-CO')}</span></td>
            <td>
                <button type="button" class="btn btn-outline-danger btn-sm btn-quitar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </td>
        `;
        detallesBody.appendChild(fila);
    }

    function actualizarCalculos() {
        let totalAcumulado = 0;
        detallesBody.querySelectorAll('tr').forEach(fila => {
            const select = fila.querySelector('.select-producto');
            const selectedOption = select.selectedOptions[0];
            
            const precio = parseFloat(selectedOption?.dataset.precio || 0);
            const cantidad = parseInt(fila.querySelector('.input-cantidad').value || 0);
            const subtotal = precio * cantidad;

            fila.querySelector('.txt-precio').textContent = precio.toLocaleString('es-CO');
            fila.querySelector('.txt-subtotal').textContent = subtotal.toLocaleString('es-CO');
            totalAcumulado += subtotal;
        });
        
        totalSpan.textContent = totalAcumulado.toLocaleString('es-CO');
        totalInput.value = totalAcumulado;
    }

    // EVENTOS
    document.addEventListener('DOMContentLoaded', inicializarVista);

    document.getElementById('btn-agregar-fila').onclick = () => {
        agregarFila();
        actualizarCalculos();
    };

    detallesBody.oninput = (e) => { 
        if (e.target.matches('.select-producto, .input-cantidad')) {
            actualizarCalculos();
        } 
    };

    detallesBody.onclick = (e) => { 
        if (e.target.closest('.btn-quitar')) { 
            e.target.closest('tr').remove(); 
            actualizarCalculos(); 
        } 
    };
</script>
@endpush

