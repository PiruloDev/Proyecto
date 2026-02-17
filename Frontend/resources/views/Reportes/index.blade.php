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
        <!-- Sidebar Component -->
        @include('components.admin-sidebar')

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4 main-content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2"><i class="bi bi-receipt me-2"></i>Órdenes de Salida</h1>
                <button class="btn-nuevo" onclick="abrirModal()">
                    <i class="bi bi-plus-circle me-1"></i>Nueva Orden
                </button>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- ============================
                 ÓRDENES DE SALIDA
                 ============================ --}}
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
                        <div class="info-row"><span class="info-label">Pedido:</span><span class="info-value">{{ $venta->ID_PEDIDO }}</span></div>
                        <div class="total-factura">
                            <div class="total-label">TOTAL FACTURA</div>
                            <div class="total-amount">${{ number_format($venta->TOTAL_FACTURA,2,',','.') }}</div>
                        </div>
                    </div>
                    <div class="card-actions">
                        <button class="btn-action btn-editar" onclick="editarModal(
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

{{-- MODAL OVERLAY --}}
<div id="modal-overlay" onclick="cerrarModal()"></div>

{{-- MODAL BOX --}}
<div id="modal-box">
    <div class="modal-header">
        <h2 id="modal-titulo">Nueva Orden</h2>
        <button class="btn-close-modal" onclick="cerrarModal()" type="button">&times;</button>
    </div>

    <form id="formulario-modal" method="POST" action="{{ route('ordenes.salida.store') }}">
        @csrf
        <div id="method-field"></div>

        <div class="form-group">
            <label for="ID_CLIENTE">ID Cliente</label>
            <input type="number" name="ID_CLIENTE" id="ID_CLIENTE" required>
        </div>

        <div class="form-group">
            <label for="ID_PEDIDO">ID Pedido</label>
            <input type="number" name="ID_PEDIDO" id="ID_PEDIDO" required>
        </div>

        <div class="form-group">
            <label for="FECHA_FACTURACION">Fecha Facturación</label>
            <input type="datetime-local" name="FECHA_FACTURACION" id="FECHA_FACTURACION" required>
        </div>

        <div class="form-group">
            <label for="TOTAL_FACTURA">Total Factura</label>
            <input type="number" step="0.01" name="TOTAL_FACTURA" id="TOTAL_FACTURA" required>
        </div>

        <button type="submit" class="btn-submit">Guardar Orden</button>
    </form>
</div>

<script>
function abrirModal() {
    document.getElementById('modal-overlay').classList.add('show');
    document.getElementById('modal-box').classList.add('show');

    document.getElementById('modal-titulo').textContent = 'Nueva Orden';
    document.getElementById('formulario-modal').action = '{{ route("ordenes.salida.store") }}';
    document.getElementById('method-field').innerHTML = '';
    document.getElementById('formulario-modal').reset();

    document.body.style.overflow = 'hidden';
}

function editarModal(id, cliente, pedido, fecha, total) {
    document.getElementById('modal-overlay').classList.add('show');
    document.getElementById('modal-box').classList.add('show');

    document.getElementById('modal-titulo').textContent = 'Editar Orden #' + id;
    document.getElementById('formulario-modal').action = '/reportes/ordenes-salida/' + id;
    document.getElementById('method-field').innerHTML = '<input type="hidden" name="_method" value="PATCH">';

    document.getElementById('ID_CLIENTE').value = cliente;
    document.getElementById('ID_PEDIDO').value = pedido;
    document.getElementById('FECHA_FACTURACION').value = fecha;
    document.getElementById('TOTAL_FACTURA').value = total;

    document.body.style.overflow = 'hidden';
}

function cerrarModal() {
    document.getElementById('modal-overlay').classList.remove('show');
    document.getElementById('modal-box').classList.remove('show');
    document.body.style.overflow = 'auto';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModal();
    }
});

// Actualizar nombre y rol del administrador en sidebar
document.addEventListener('DOMContentLoaded', function() {
    if (typeof AuthManager !== 'undefined' && AuthManager.isAuthenticated()) {
        const userData = AuthManager.getUserData();
        const userRole = AuthManager.getRole();

        const adminNameElement = document.getElementById('admin-name');
        const adminRoleElement = document.getElementById('admin-role');

        if (adminNameElement && userData && userData.nombre) {
            adminNameElement.textContent = userData.nombre;
        }

        if (adminRoleElement && userRole) {
            adminRoleElement.textContent = userRole.charAt(0) + userRole.slice(1).toLowerCase();
        }
    }
});
</script>
@endsection
