@extends('layouts.app')

@section('title', 'Órdenes de Salida - Panadería')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="{{ asset('css/variables.css') }}" rel="stylesheet">
<link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet">
<link href="{{ asset('css/reportes-estadisticas.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('components.admin-sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-4 main-content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="dashboard-page-title">Órdenes de Salida</h1>
                <button class="btn btn-panaderia-action -nuevo" onclick="abrirModal()">
                    <i class="bi bi-plus-circle me-1"></i>Nueva Orden
                </button>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($ventas && count($ventas) > 0)
                <div class="cards-grid">
                @foreach($ventas as $venta)
                    <div class="orden-card">
                        <div class="card-header-custom">
                            <span class="orden-id">Orden #{{ $venta->ID_FACTURA }}</span>
                            <span class="fecha-badge">{{ \Carbon\Carbon::parse($venta->FECHA_FACTURACION)->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="card-body-custom">
                            <div class="info-row">
                                <span class="info-label">Cliente:</span>
                                <span class="info-value">{{ $venta->cliente->NOMBRE_CLI ?? 'Sin cliente' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Pedido:</span>
                                <span class="info-value">{{ $venta->ID_PEDIDO }}</span>
                            </div>
                            <div class="total-factura">
                                <div class="total-label">TOTAL FACTURA</div>
                                <div class="total-amount">${{ number_format($venta->TOTAL_FACTURA,2,',','.') }}</div>
                            </div>
                        </div>
                        <div class="card-actions">
                            <button class="btn btn-panaderia-action -action -editar" onclick="editarModal(
                                {{ $venta->ID_FACTURA }},
                                {{ $venta->ID_CLIENTE }},
                                {{ $venta->ID_PEDIDO }},
                                '{{ \Carbon\Carbon::parse($venta->FECHA_FACTURACION)->format('Y-m-d\TH:i') }}',
                                {{ $venta->TOTAL_FACTURA }}
                            )">Editar</button>
                            <form action="{{ route('ordenes.salida.destroy',$venta->ID_FACTURA) }}" method="POST" style="flex: 1;">
                                @csrf
                                @method('DELETE')
                                <button class="btn-action btn-eliminar" type="submit" onclick="return confirm('¿Seguro que deseas eliminar esta orden?')">Eliminar</button>
                            </form>
                        </div>
                    </div>
                @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>No hay órdenes de salida disponibles.
                </div>
            @endif
        </main>
    </div>
</div>

{{-- Modal Bootstrap para Órdenes --}}
<div class="modal fade" id="ordenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px;">
            <div class="modal-header text-white" style="border-radius: 12px 12px 0 0; background: #a67c52;">
                <h5 class="modal-title" id="modal-titulo">Nueva Orden</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="cerrarModal()"></button>
            </div>
            <form id="formulario-modal" method="POST" action="{{ route('ordenes.salida.store') }}">
                @csrf
                <div id="method-field"></div>
                <div class="modal-body row g-3">
                    {{-- Select con nombres reales de clientes --}}
                    <div class="col-12">
                        <label for="ID_CLIENTE" class="form-label fw-bold">Cliente</label>
                        <select class="form-select" name="ID_CLIENTE" id="ID_CLIENTE" required>
                            <option value="" disabled selected>-- Selecciona un cliente --</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->ID_CLIENTE }}">
                                    {{ $cliente->NOMBRE_CLI }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Select pedidos filtrados por cliente --}}
                    <div class="col-12">
                        <label for="ID_PEDIDO" class="form-label fw-bold">Pedido</label>
                        <select class="form-select" name="ID_PEDIDO" id="ID_PEDIDO" required>
                            <option value="" disabled selected>-- Primero selecciona un cliente --</option>
                            @foreach($pedidos as $pedido)
                                <option value="{{ $pedido->ID_PEDIDO }}" data-cliente="{{ $pedido->ID_CLIENTE }}">
                                    Pedido #{{ $pedido->ID_PEDIDO }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="FECHA_FACTURACION" class="form-label fw-bold">Fecha Facturación</label>
                        <input type="datetime-local" class="form-control" name="FECHA_FACTURACION" id="FECHA_FACTURACION" required>
                    </div>

                    <div class="col-12">
                        <label for="TOTAL_FACTURA" class="form-label fw-bold">Total Factura</label>
                        <input type="number" step="0.01" class="form-control" name="TOTAL_FACTURA" id="TOTAL_FACTURA" required>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal" onclick="cerrarModal()">Cancelar</button>
                    <button type="submit" class="btn text-white" style="background: #a67c52;">Guardar Orden</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Reutilizable: filtra pedidos según clienteId dado
    function filtrarPedidosPorCliente(clienteId) {
        const selectPedido = document.getElementById('ID_PEDIDO');
        const opciones = selectPedido.querySelectorAll('option[data-cliente]');

        selectPedido.value = '';
        let hayOpciones = false;

        opciones.forEach(opt => {
            if (opt.dataset.cliente === String(clienteId)) {
                opt.style.display = '';
                hayOpciones = true;
            } else {
                opt.style.display = 'none';
            }
        });

        selectPedido.querySelector('option[disabled]').textContent = hayOpciones
            ? '-- Selecciona un pedido --'
            : '-- Este cliente no tiene pedidos --';
    }

    // Evento: cuando el usuario cambia el cliente en el select
    document.getElementById('ID_CLIENTE').addEventListener('change', function () {
        filtrarPedidosPorCliente(this.value);
    });

    function abrirModal() {
        bootstrap.Modal.getOrCreateInstance(document.getElementById('ordenModal')).show();
        document.getElementById('modal-titulo').textContent = 'Nueva Orden';
        document.getElementById('formulario-modal').action = '{{ route("ordenes.salida.store") }}';
        document.getElementById('method-field').innerHTML = '';
        document.getElementById('formulario-modal').reset();

        // Ocultar todos los pedidos al abrir vacío
        const selectPedido = document.getElementById('ID_PEDIDO');
        selectPedido.querySelectorAll('option[data-cliente]').forEach(opt => opt.style.display = 'none');
        selectPedido.querySelector('option[disabled]').textContent = '-- Primero selecciona un cliente --';
        selectPedido.value = '';
    }

    function editarModal(id, clienteId, pedidoId, fecha, total) {
        bootstrap.Modal.getOrCreateInstance(document.getElementById('ordenModal')).show();
        document.getElementById('modal-titulo').textContent = 'Editar Orden #' + id;
        document.getElementById('formulario-modal').action = '/reportes/ordenes-salida/' + id;
        document.getElementById('method-field').innerHTML = '<input type="hidden" name="_method" value="PATCH">';

        // Seleccionar cliente y filtrar pedidos automáticamente
        document.getElementById('ID_CLIENTE').value = clienteId;
        filtrarPedidosPorCliente(clienteId);
        document.getElementById('ID_PEDIDO').value = pedidoId;

        document.getElementById('FECHA_FACTURACION').value = fecha;
        document.getElementById('TOTAL_FACTURA').value = total;
    }

    function cerrarModal() {
        const modalInstance = bootstrap.Modal.getInstance(document.getElementById('ordenModal'));
        if(modalInstance) modalInstance.hide();
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof AuthManager !== 'undefined' && AuthManager.isAuthenticated()) {
            const userData = AuthManager.getUserData();
            const userRole = AuthManager.getRole();
            const adminNameElement = document.getElementById('admin-name');
            const adminRoleElement = document.getElementById('admin-role');
            if (adminNameElement && userData?.nombre) adminNameElement.textContent = userData.nombre;
            if (adminRoleElement && userRole) adminRoleElement.textContent = userRole.charAt(0) + userRole.slice(1).toLowerCase();
        }
    });
</script>
@endsection