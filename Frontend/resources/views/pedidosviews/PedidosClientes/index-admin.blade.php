@extends('layouts.app')

@section('title', 'Gestión de Pedidos (Administrador)')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/stylempleado.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
      
        .main-content { min-height: 100vh; background-color: #fdfaf6; }
        .content-wrapper { padding: 2rem; }
        
        
        @media (max-width: 991.98px) {
            .content-wrapper { padding: 1rem; }
            .table-responsive { border: 0; }
            
            .table thead {
                display: none; 
            }
            
            .table tbody tr {
                display: block;
                margin-bottom: 1.5rem;
                border: 1px solid #dee2e6 !important;
                border-radius: 0.5rem;
                background-color: #fff;
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            }
            
            .table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: right;
                padding: 0.75rem 1rem !important;
                border-bottom: 1px solid #eee !important;
            }
            
            .table tbody td::before {
                content: attr(data-label);
                font-weight: bold;
                text-transform: uppercase;
                font-size: 0.8rem;
                color: #a67c52;
                margin-right: 1rem;
                text-align: left;
            }

            .table tbody td:last-child {
                border-bottom: 0 !important;
                justify-content: center;
            }
            
            .btn-toolbar {
                flex-direction: column;
                gap: 10px;
            }
            .btn-toolbar .btn {
                width: 100%;
            }
        }

        .modal-header { background: #a67c52; color: white; }
        .btn-quitar { border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; padding: 0; }
        .badge-estado { font-size: 0.85rem; padding: 0.5em 0.8em; }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('components.admin-sidebar') 
        
        <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
            <div class="content-wrapper">
                
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                    <h2 class="dashboard-page-title">Gestión de Pedidos</h2>
                    <div class="btn-toolbar mt-2 mt-md-0">
                        <button type="button" class="btn btn-panaderia-action me-md-2" id="btn-create-pedido" >
                            <i class="bi bi-plus-circle"></i> Nuevo Pedido
                        </button>
                        <a href="{{ route('admin.pedidos.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-sync"></i> Recargar
                        </a>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif  

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Empleado</th>
                                <th>Estado</th>
                                <th>Total</th>
                                <th>Ingreso</th>
                                <th>Entrega</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pedidos as $pedido)
                                <tr>
                                    <td data-label="ID">{{ $pedido['id_PEDIDO'] }}</td>
                                    <td data-label="Cliente">{{ $pedido['nombre_cliente'] ?? 'ID: '.$pedido['cliente_id'] }}</td>
                                    <td data-label="Empleado">{{ $pedido['nombre_empleado'] ?? 'ID: '.$pedido['empleado_id'] }}</td>
                                    <td data-label="Estado">
                                        <span class="badge bg-info text-dark badge-estado">
                                            {{ $pedido['nombre_estado'] ?? 'Estado: '.$pedido['estado_pedido_id'] }}
                                        </span>
                                    </td>
                                    <td data-label="Total" class="fw-bold text-success">
                                        ${{ number_format($pedido['total_producto'] ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td data-label="Ingreso">{{ $pedido['fecha_ingreso'] ?? 'N/A' }}</td>
                                    <td data-label="Entrega">
                                        @if(!empty($pedido['fecha_entrega']) && $pedido['fecha_entrega'] !== 'N/A')
                                            <div class="d-flex flex-column align-items-md-start align-items-end">
                                                <span class="fw-bold">{{ \Carbon\Carbon::parse($pedido['fecha_entrega'])->format('d/m/Y') }}</span>
                                                @php $hora = \Carbon\Carbon::parse($pedido['fecha_entrega'])->format('H:i'); @endphp
                                                @if($hora !== '23:59')
                                                    <small class="text-muted"><i class="far fa-clock me-1"></i>{{ $hora }}</small>
                                                @else
                                                    <small class="text-warning" style="font-size: 0.75rem;">Hora por definir</small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted small">No asignada</span>
                                        @endif
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <div class="btn-actions">
                                            <button type="button" class="btn btn-success btn-sm me-1 btn-edit-pedido" data-pedido="{{ json_encode($pedido) }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form method="POST" action="{{ route('admin.pedidos.destroy', $pedido['id_PEDIDO']) }}" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar el pedido #{{ $pedido['id_PEDIDO'] }}?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                                        <span class="text-muted">No hay pedidos registrados en el sistema.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<div class="modal fade" id="pedidoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 12px;">
            <div class="modal-header text-white" style="border-radius: 12px 12px 0 0;">
                <h5 class="modal-title" id="pedidoModalLabel">Gestión de Pedido</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="pedidoForm" method="POST">
                @csrf
                <input type="hidden" name="_method" value="POST" id="formMethod"> 
                <div class="modal-body row g-3">
                    <input type="hidden" name="id_PEDIDO" id="modal_id_PEDIDO">
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Cliente</label>
                        <select class="form-select" id="modal_ID_CLIENTE" name="ID_CLIENTE" required>
                            <option value="">Seleccione Cliente...</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente['Id:'] ?? '' }}">
                                    {{ $cliente['Nombre:'] ?? 'Sin Nombre' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Empleado</label>
                        <select class="form-select" id="modal_ID_EMPLEADO" name="ID_EMPLEADO" required>
                            <option value="">Seleccione Empleado...</option>
                            @foreach($empleados as $empleado)
                                <option value="{{ $empleado['Id:'] ?? '' }}">
                                    {{ $empleado['Nombre:'] ?? 'Sin Nombre' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Estado del Pedido</label>
                        <select class="form-select" id="modal_ID_ESTADO_PEDIDO" name="ID_ESTADO_PEDIDO" required>
                            <option value="">Seleccione Estado...</option>
                            @foreach($estados as $estado)
                                <option value="{{ $estado['Id:'] ?? $estado['id_ESTADO_PEDIDO'] ?? $estado['id'] ?? '' }}">
                                    {{ $estado['Nombre:'] ?? $estado['nombre_ESTADO'] ?? $estado['nombre'] ?? 'Sin Estado' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Fecha y Hora de Entrega</label>
                        <input type="datetime-local" class="form-control" id="modal_FECHA_ENTREGA" name="FECHA_ENTREGA">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Total Producto ($)</label>
                        <input type="number" class="form-control" id="modal_TOTAL_PRODUCTO" name="TOTAL_PRODUCTO" readonly>
                    </div>

                    <div class="col-12 mt-4">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                            <h6 class="fw-bold text-secondary mb-0"><i class="fas fa-shopping-basket me-2"></i>Artículos</h6>
                            <button type="button" class="btn btn-sm btn-success" id="btn-agregar-fila"><i class="fas fa-plus"></i> Añadir</button>
                        </div>
                        <div class="table-responsive border rounded bg-white">
                            <table class="table table-sm table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Producto</th>
                                        <th class="text-center">Cant.</th>
                                        <th class="text-end">Precio Unit.</th>
                                        <th class="text-end pe-3">Subtotal</th>
                                        <th class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody id="detalles-pedido-body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white" style="background: #a67c52;">Guardar Pedido</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let listaProductosGlobal = [];

    async function cargarProductosDesdeJava() {
        try {
            const response = await fetch('http://32.193.167.191:8080/productos');
            const data = await response.json();
            listaProductosGlobal = data.map(p => ({
                id: p.id_PRODUCTO || p.idProducto || p["Id Producto:"],
                nombre: p.nombre_PRODUCTO || p.nombreProducto || p["Nombre Producto:"],
                precio: p.precio_UNITARIO || p.precio || p["Precio:"] || 0
            }));
        } catch (error) {
            console.error("Error productos:", error);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        cargarProductosDesdeJava();

        const modal = new bootstrap.Modal(document.getElementById('pedidoModal'));
        const form = document.getElementById('pedidoForm');
        const detallesBody = document.getElementById('detalles-pedido-body');

        function agregarFila(detalle = null) {
            const emptyRow = detallesBody.querySelector('tr td[colspan]');
            if (emptyRow) detallesBody.innerHTML = '';

            const selectedId = detalle ? (detalle.id_PRODUCTO || detalle.idProducto || detalle.ID_PRODUCTO) : '';
            const cantidad = detalle ? (detalle.cantidad_PRODUCTO || detalle.cantidadProducto || detalle.CANTIDAD_PRODUCTO) : 1;
            const precio = detalle ? (detalle.precio_UNITARIO || detalle.precioUnitario || detalle.PRECIO_UNITARIO) : 0;
            const subtotal = detalle ? (detalle.subtotal || (precio * cantidad)) : 0;

            let opciones = listaProductosGlobal.map(p => 
                `<option value="${p.id}" data-precio="${p.precio}" ${p.id == selectedId ? 'selected' : ''}>${p.nombre}</option>`
            ).join('');

            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td data-label="Producto">
                    <select name="productos[]" class="form-select form-select-sm select-producto" required>
                        <option value="">Seleccione...</option>
                        ${opciones}
                    </select>
                </td>
                <td data-label="Cant."><input type="number" name="cantidades[]" class="form-control form-control-sm text-center input-cantidad" value="${cantidad}" min="1" required></td>
                <td data-label="Precio Unit." class="text-end align-middle">
                    <span class="precio-unit text-muted">$${Number(precio).toLocaleString('es-CO')}</span>
                    <input type="hidden" name="precios_unitarios[]" class="input-precio-unitario" value="${precio}">
                </td>
                <td data-label="Subtotal" class="text-end align-middle">
                    <span class="subtotal-fila fw-bold">$${Number(subtotal).toLocaleString('es-CO')}</span>
                    <input type="hidden" name="subtotales[]" class="input-subtotal" value="${subtotal}">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm btn-quitar">
                        <i class="fas fa-times"></i>
                    </button>
                </td>`;
            
            detallesBody.appendChild(fila);
            actualizarTotalPedido();
        }

        function actualizarTotalPedido() {
            let totalGeneral = 0;
            document.querySelectorAll('.input-subtotal').forEach(input => {
                totalGeneral += parseFloat(input.value) || 0;
            });
            document.getElementById('modal_TOTAL_PRODUCTO').value = totalGeneral;
        }

        document.getElementById('btn-agregar-fila').onclick = function(e) {
            e.preventDefault();
            agregarFila();
        };

        document.getElementById('btn-create-pedido').addEventListener('click', () => {
            document.getElementById('pedidoModalLabel').textContent = 'Crear Nuevo Pedido';
            form.action = "{{ route('admin.pedidos.store') }}";
            document.getElementById('formMethod').value = 'POST';
            form.reset();
            detallesBody.innerHTML = '<tr><td colspan="5" class="text-center py-3 text-muted">Añada productos.</td></tr>';
            modal.show();
        });

        detallesBody.addEventListener('input', (e) => {
            if (e.target.classList.contains('select-producto') || e.target.classList.contains('input-cantidad')) {
                const fila = e.target.closest('tr');
                const select = fila.querySelector('.select-producto');
                const cant = parseInt(fila.querySelector('.input-cantidad').value) || 0;
                const precio = parseFloat(select.options[select.selectedIndex]?.dataset.precio) || 0;
                const subtotal = precio * cant;
                
                fila.querySelector('.precio-unit').textContent = `$${Number(precio).toLocaleString('es-CO')}`;
                fila.querySelector('.subtotal-fila').textContent = `$${Number(subtotal).toLocaleString('es-CO')}`;
                fila.querySelector('.input-precio-unitario').value = precio;
                fila.querySelector('.input-subtotal').value = subtotal;
                
                actualizarTotalPedido();
            }
        });

        detallesBody.addEventListener('click', (e) => {
            const btnQuitar = e.target.closest('.btn-quitar');
            if (btnQuitar) {
                btnQuitar.closest('tr').remove();
                if (detallesBody.children.length === 0) {
                    detallesBody.innerHTML = '<tr><td colspan="5" class="text-center py-3 text-muted">Añada productos.</td></tr>';
                }
                actualizarTotalPedido();
            }
        });

        document.addEventListener('click', function(e) {
            const btnEdit = e.target.closest('.btn-edit-pedido');
            if (btnEdit) {
                const data = JSON.parse(btnEdit.getAttribute('data-pedido'));
                document.getElementById('pedidoModalLabel').textContent = 'Editar Pedido #' + data.id_PEDIDO;
                form.action = "{{ url('admin/pedidos') }}/" + data.id_PEDIDO;
                document.getElementById('formMethod').value = 'PUT';

                document.getElementById('modal_id_PEDIDO').value = data.id_PEDIDO;
                document.getElementById('modal_ID_CLIENTE').value = data.id_CLIENTE || data.ID_CLIENTE;
                document.getElementById('modal_ID_EMPLEADO').value = data.id_EMPLEADO || data.ID_EMPLEADO;
                document.getElementById('modal_ID_ESTADO_PEDIDO').value = data.id_ESTADO_PEDIDO || data.ID_ESTADO_PEDIDO;
                document.getElementById('modal_TOTAL_PRODUCTO').value = data.total_PRODUCTO || data.TOTAL_PRODUCTO;
                
                if (data.fecha_ENTREGA && data.fecha_ENTREGA !== 'N/A') {
                    const fechaFormateada = data.fecha_ENTREGA.replace(" ", "T").substring(0, 16);
                    document.getElementById('modal_FECHA_ENTREGA').value = fechaFormateada;
                }

                detallesBody.innerHTML = '';
                const detalles = data.detalles || data.detalle_pedidos || [];
                if (detalles.length > 0) {
                    detalles.forEach(d => agregarFila(d));
                } else {
                    detallesBody.innerHTML = '<tr><td colspan="5" class="text-center py-3 text-muted">Sin detalles.</td></tr>';
                }
                modal.show();
            }
        });
    });
</script>
@endpush

