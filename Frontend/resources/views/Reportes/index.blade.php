@extends('layouts.app')

@section('title', 'Estadísticas - Panadería')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="{{ asset('css/variables.css') }}" rel="stylesheet">
<link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet">
<style>
    body {
        background: linear-gradient(135deg, #FFF8E7 0%, #FFE9D0 100%);
    }

    .btn-nuevo {
        background: #a67c52;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-nuevo:hover {
        background: #8b6745;
        color: white;
    }

    .cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.25rem;
        margin-top: 1.5rem;
    }

    .orden-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 2px 4px rgba(139, 90, 43, 0.1);
        transition: all 0.3s ease;
        border: 1px solid #F5E6D3;
    }

    .orden-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(139, 90, 43, 0.2);
    }

    .card-header-custom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #F5E6D3;
    }

    .orden-id {
        background: #a67c52;
        color: white;
        padding: 0.35rem 0.75rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .card-body-custom {
        margin: 1rem 0;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #FFF8E7;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        color: #8B5A2B;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .info-value {
        color: #5C4033;
        font-size: 0.9rem;
    }

    .total-factura {
        background: #FFF8E7;
        padding: 1rem;
        border-radius: 10px;
        margin: 1rem 0;
        text-align: center;
    }

    .total-label {
        color: #8B5A2B;
        font-size: 0.85rem;
        font-weight: 500;
        margin-bottom: 0.25rem;
    }

    .total-amount {
        color: #5C4033;
        font-size: 1.5rem;
        font-weight: 700;
    }

    .card-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .btn-action {
        flex: 1;
        padding: 0.75rem;
        border-radius: 10px;
        border: none;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-editar {
        background: #46a169;
        color: white;
    }

    .btn-editar:hover {
        background: #3d8f5a;
        color: white;
    }

    .btn-eliminar {
        background: #fc7272;
        color: white;
    }

    .btn-eliminar:hover {
        background: #f85252;
        color: white;
    }

    .alert {
        padding: 1rem 1.5rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        font-weight: 500;
    }

    .alert-success {
        background: #D4EDDA;
        color: #155724;
        border: 2px solid #C3E6CB;
    }

    .alert-error {
        background: #F8D7DA;
        color: #721C24;
        border: 2px solid #F5C6CB;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 8px rgba(139, 90, 43, 0.15);
    }

    .empty-icon {
        font-size: 4rem;
        color: #D4A574;
        margin-bottom: 1rem;
    }

    .empty-text {
        color: #8B5A2B;
        font-size: 1.2rem;
        font-weight: 500;
    }

    .fecha-badge {
        background: #FFF8E7;
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.85rem;
        color: #8B5A2B;
    }

    #modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        z-index: 9998;
        display: none;
    }

    #modal-overlay.show {
        display: block;
    }

    #modal-box {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #FFF8E7;
        padding: 2.5rem;
        border-radius: 15px;
        width: 90%;
        max-width: 550px;
        box-shadow: 0 10px 40px rgba(139, 90, 43, 0.5);
        z-index: 9999;
        max-height: 90vh;
        overflow-y: auto;
        display: none;
    }

    #modal-box.show {
        display: block;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #D4A574;
    }

    .modal-header h2 {
        color: #8B5A2B;
        font-size: 1.75rem;
        margin: 0;
    }

    .btn-close-modal {
        background: none;
        border: none;
        font-size: 2.5rem;
        color: #8B5A2B;
        cursor: pointer;
        line-height: 1;
        padding: 0;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .btn-close-modal:hover {
        background: rgba(139, 90, 43, 0.1);
        color: #5C4033;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #8B5A2B;
        font-size: 1rem;
    }

    .form-group input {
        width: 100%;
        padding: 0.85rem;
        border-radius: 10px;
        border: 2px solid #D4A574;
        box-sizing: border-box;
        font-size: 1rem;
        background: white;
        transition: all 0.3s ease;
    }

    .form-group input:focus {
        outline: none;
        border-color: #B8935F;
        box-shadow: 0 0 0 4px rgba(212, 165, 116, 0.2);
    }

    .btn-submit {
        background: #a67c52;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-weight: 600;
        width: 100%;
        transition: all 0.3s ease;
        font-size: 1rem;
    }

    .btn-submit:hover {
        background: #8b6745;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Component -->
        @include('components.admin-sidebar')

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2"><i class="bi bi-graph-up me-2"></i>Estadísticas y Reportes</h1>
                <button onclick="abrirModal()" class="btn-nuevo">
                    <i class="bi bi-plus-circle me-1"></i> Nueva Orden
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
                        <div class="info-row"><span class="info-label">Cliente:</span><span class="info-value">{{ $venta->ID_CLIENTE }}</span></div>
                        <div class="info-row"><span class="info-label">Pedido:</span><span class="info-value">{{ $venta->ID_PEDIDO }}</span></div>
                        <div class="total-factura">
                            <div class="total-label">TOTAL FACTURA</div>
                            <div class="total-amount">${{ number_format($venta->TOTAL_FACTURA,2,',','.') }}</div>
                        </div>
                    </div>
                    <div class="card-actions">
                        <button class="btn-action btn-editar" onclick="editarModal({{ $venta->ID_FACTURA }}, {{ $venta->ID_CLIENTE }}, {{ $venta->ID_PEDIDO }}, '{{ \Carbon\Carbon::parse($venta->FECHA_FACTURACION)->format('Y-m-d\TH:i') }}', {{ $venta->TOTAL_FACTURA }})">Editar</button>
                        <form action="{{ route('ordenes.salida.destroy',$venta->ID_FACTURA) }}" method="POST" style="flex: 1;">
                            @csrf
                            @method('DELETE')
                            <button class="btn-action btn-eliminar" type="submit" onclick="return confirm('¿Seguro que deseas eliminar esta orden?')">Eliminar</button>
                        </form>
                    </div>
                </div>
            @endforeach
            @else
                <div class="empty-state">
                    <div class="empty-icon">📦</div>
                    <p class="empty-text">No hay órdenes de salida</p>
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
