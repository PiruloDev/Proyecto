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
        .btn-update { background-color: #ffc107; border: none; font-weight: bold; color: #212529; }
        .btn-update:hover { background-color: #e0a800; }
        .text-success-custom { color: #28a745; }
        .hidden-inputs { display: none; }
        /* Estilo para cuando la tabla está vacía o cargando */
        #detalles-body tr td { vertical-align: middle; }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        {{-- Sidebar del Empleado --}}
        @include('components.employee-sidebar') 
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4 main-content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="dashboard-page-title">Editar Pedido #{{ $pedido['id_PEDIDO'] }}</h1>
                <a href="{{ route('pedidos.index') }}" class="btn btn-outline-secondary shadow-sm">
                    <i class="fas fa-arrow-left"></i> Volver al Listado
                </a>
            </div>

            <form action="{{ route('pedidos.update', $pedido['id_PEDIDO']) }}" method="POST" id="editPedidoForm">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    {{-- Columna Izquierda: Info General --}}
                    <div class="col-lg-4">
                        <div class="card p-4">
                            <h5 class="text-secondary border-bottom pb-2 mb-3">Información del Pedido</h5>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">ID Cliente</label>
                                <input type="number" class="form-control" name="ID_CLIENTE" value="{{ $pedido['id_CLIENTE'] }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">ID Empleado</label>
                                <input type="number" class="form-control bg-light" name="ID_EMPLEADO" value="{{ $pedido['id_EMPLEADO'] }}" required readonly>
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

                    {{-- Columna Derecha: Detalle de Productos --}}
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
                                            <th>Precio Unit.</th>
                                            <th>Subtotal</th>
                                            <th width="50"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="detalles-body">
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                                Sincronizando con inventario...
                                            </td>
                                        </tr>
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

    async function inicializarVista() {
        try {
            console.log("Iniciando carga de productos desde Java...");
            const response = await fetch('http://localhost:8080/productos');
            if (!response.ok) throw new Error("Fallo al conectar con Java");
            
            const data = await response.json();
            
            // NORMALIZACIÓN AGRESIVA: Mapeamos cualquier posibilidad de nombre de campo
            listaProductosGlobal = data.map(p => ({
                id: p["Id Producto:"] || p.id_PRODUCTO || p.idProducto || p.ID_PRODUCTO || p.id,
                nombre: p["Nombre Producto:"] || p.nombre_PRODUCTO || p.nombreProducto || p.NOMBRE_PRODUCTO || p.nombre,
                precio: parseFloat(p["Precio:"] || p.precio_UNITARIO || p.precio || p.PRECIO_UNITARIO || 0)
            }));

            console.log("Inventario normalizado (Check ID y Nombre):", listaProductosGlobal);
            
            // Solo después de normalizar, intentamos renderizar el pedido
            renderizarDetallesExistentes();

        } catch (e) { 
            console.error("Error crítico en inicializarVista:", e);
            detallesBody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error de conexión con el inventario (Java)</td></tr>';
        }
    }

    function renderizarDetallesExistentes() {
    const pedidoCompleto = @json($pedido);
    console.log("--- DEBUG DE DATOS ---");
    console.log("Objeto Pedido recibido:", pedidoCompleto);
    
    // Buscamos los detalles en todas las llaves posibles que devuelva tu API Java
    const detalles = pedidoCompleto.detalles || 
                     pedidoCompleto.detalle_pedidos || 
                     pedidoCompleto.detallesPedido || 
                     pedidoCompleto.lista_productos || [];

    console.log("Detalles extraídos:", detalles);

    detallesBody.innerHTML = '';

    if (detalles && detalles.length > 0) {
        detalles.forEach(d => {
            console.log("Procesando detalle individual:", d);
            agregarFila(d);
        });
    } else {
        console.warn("ALERTA: El array de detalles llegó vacío desde el controlador.");
        detallesBody.innerHTML = '<tr><td colspan="5" class="text-center py-3 text-muted">No se encontraron artículos para este pedido en el sistema.</td></tr>';
    }
    actualizarCalculos();
}

    function agregarFila(detalle = null) {
        if (detallesBody.querySelector('td[colspan]')) detallesBody.innerHTML = '';

        // Extraer el ID del producto que viene del detalle de Laravel
        // Importante: Laravel suele enviar 'id_PRODUCTO' o 'id_producto'
        const prodIdFromLaravel = detalle ? (detalle.id_PRODUCTO || detalle.id_producto || detalle.idProducto || detalle.ID_PRODUCTO) : '';
        const cant = detalle ? (detalle.cantidad_PRODUCTO || detalle.cantidad_producto || detalle.cantidadProducto || 1) : 1;
        
        // Buscamos el producto en la lista de Java usando el ID de Laravel
        const productoEncontrado = listaProductosGlobal.find(p => String(p.id) === String(prodIdFromLaravel));
        
        if (detalle && !productoEncontrado) {
            console.error(`No se encontró el producto ID ${prodIdFromLaravel} en el inventario de Java.`);
        }

        const precioFinal = productoEncontrado ? productoEncontrado.precio : 0;
        const subtotal = precioFinal * cant;

        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td>
                <select name="productos[]" class="form-select select-producto" required>
                    <option value="">Seleccione...</option>
                    ${listaProductosGlobal.map(p => `
                        <option value="${p.id}" data-precio="${p.precio}" ${String(p.id) === String(prodIdFromLaravel) ? 'selected' : ''}>
                            ${p.nombre}
                        </option>`).join('')}
                </select>
            </td>
            <td>
                <input type="number" name="cantidades[]" class="form-control text-center input-cantidad" value="${cant}" min="1">
            </td>
            <td>$<span class="txt-precio">${Number(precioFinal).toLocaleString('es-CO')}</span></td>
            <td class="fw-bold text-dark">$<span class="txt-subtotal">${Number(subtotal).toLocaleString('es-CO')}</span></td>
            <td>
                <button type="button" class="btn btn-outline-danger btn-sm btn-quitar"><i class="bi bi-x-lg"></i></button>
                <div class="hidden-inputs">
                    <input type="hidden" name="precios_unitarios[]" value="${precioFinal}">
                    <input type="hidden" name="subtotales[]" value="${subtotal}">
                </div>
            </td>
        `;
        detallesBody.appendChild(fila);
    }

    function actualizarCalculos() {
        let totalAcumulado = 0;
        detallesBody.querySelectorAll('tr:not(:has(td[colspan]))').forEach(fila => {
            const select = fila.querySelector('.select-producto');
            const selectedOption = select.options[select.selectedIndex];
            const precio = parseFloat(selectedOption?.dataset.precio || 0);
            const cantidad = parseInt(fila.querySelector('.input-cantidad').value || 0);
            const subtotal = precio * cantidad;

            fila.querySelector('.txt-precio').textContent = precio.toLocaleString('es-CO');
            fila.querySelector('.txt-subtotal').textContent = subtotal.toLocaleString('es-CO');
            
            fila.querySelector('.hidden-inputs').innerHTML = `
                <input type="hidden" name="precios_unitarios[]" value="${precio}">
                <input type="hidden" name="subtotales[]" value="${subtotal}">
            `;
            totalAcumulado += subtotal;
        });
        totalSpan.textContent = totalAcumulado.toLocaleString('es-CO');
        totalInput.value = totalAcumulado;
    }

    // Eventos
    document.addEventListener('DOMContentLoaded', inicializarVista);
    document.getElementById('btn-agregar-fila').addEventListener('click', () => agregarFila());
    detallesBody.addEventListener('input', (e) => {
        if (e.target.classList.contains('select-producto') || e.target.classList.contains('input-cantidad')) {
            actualizarCalculos();
        }
    });
    detallesBody.addEventListener('click', (e) => {
        if (e.target.closest('.btn-quitar')) {
            e.target.closest('tr').remove();
            actualizarCalculos();
        }
    });
</script>
@endpush