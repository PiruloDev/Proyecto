@extends('layouts.app')

@section('title', 'Dashboard Empleado - Panadería')

@push('styles')
<link href="{{ asset('css/variables.css') }}" rel="stylesheet">
<link href="{{ asset('css/dashboard-employee.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    .action-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid #eee;
        border-radius: 15px;
        background: white;
    }
    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08);
    }
    .btn-quitar {
        border-radius: 50%;
        width: 28px; height: 28px;
        display: flex; align-items: center; justify-content: center;
        padding: 0;
    }
</style>
@endpush

@section('content')
<script>
    (function() {
        const logoutFlag = sessionStorage.getItem('logout_flag');
        if (logoutFlag === 'true') {
            sessionStorage.clear();
            window.location.replace('/login');
        }
    })();
</script>

<div class="container-fluid">
    <div class="row">
        @include('components.employee-sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 main-content px-4">

            <div class="section-content" id="dashboard-section">

                {{-- Encabezado estándar del dashboard --}}
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                    <div>
                        <h1 class="dashboard-page-title" id="welcome-name">Bienvenido</h1>
                        <p class="text-muted mb-0">Panel de control operativo de la panadería</p>
                    </div>
                </div>

                {{-- Alertas de sesión --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Tarjetas de estadísticas --}}
                <div class="row g-3 mb-4" id="stats-row">
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card p-3 shadow-sm rounded">
                            <div class="d-flex align-items-center">
                                <div class="card-icon p-3 rounded-circle me-3" style="background: rgba(166,124,82,0.12);">
                                    <i class="bi bi-cart-check fs-4" style="color: #a67c52;"></i>
                                </div>
                                <div>
                                    <div class="stat-number fw-bold fs-4">{{ $pedidosHoy ?? 0 }}</div>
                                    <div class="stat-label text-muted small">Pedidos Hoy</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card p-3 shadow-sm rounded">
                            <div class="d-flex align-items-center">
                                <div class="card-icon p-3 rounded-circle me-3" style="background: rgba(166,124,82,0.12);">
                                    <i class="bi bi-clock fs-4" style="color: #a67c52;"></i>
                                </div>
                                <div>
                                    <div class="stat-number fw-bold fs-4">{{ $pedidosPendientes ?? 0 }}</div>
                                    <div class="stat-label text-muted small">Pendientes</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card p-3 shadow-sm rounded">
                            <div class="d-flex align-items-center">
                                <div class="card-icon p-3 rounded-circle me-3" style="background: rgba(166,124,82,0.12);">
                                    <i class="bi bi-box-seam fs-4" style="color: #a67c52;"></i>
                                </div>
                                <div>
                                    <div class="stat-number fw-bold fs-4">{{ $productosDisponibles ?? 0 }}</div>
                                    <div class="stat-label text-muted small">Productos</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card p-3 shadow-sm rounded">
                            <div class="d-flex align-items-center">
                                <div class="card-icon p-3 rounded-circle me-3" style="background: rgba(166,124,82,0.12);">
                                    <i class="bi bi-check-circle fs-4" style="color: #a67c52;"></i>
                                </div>
                                <div>
                                    <div class="stat-number fw-bold fs-4">{{ $totalPedidos ?? 0 }}</div>
                                    <div class="stat-label text-muted small">Total Pedidos</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Acciones rápidas --}}
                <div class="orders-section" id="main-actions">
                    <div class="section-header mb-3 border-bottom pb-2">
                        <h4 class="fw-bold">Acciones Rápidas</h4>
                    </div>
                    <div class="row g-4">
                        <div class="col-12 col-md-6">
                            <div class="action-card text-center p-5 shadow-sm">
                                <div class="mb-3">
                                    <i class="bi bi-plus-circle-fill fs-1" style="color: #a67c52;"></i>
                                </div>
                                <h4 class="fw-bold">Crear Pedido</h4>
                                <p class="text-muted">Inicia una nueva orden de venta para un cliente.</p>
                                <button type="button" id="btn-create-pedido"
                                    class="btn btn-panaderia-action mt-2 px-4">
                                    Nueva Orden
                                </button>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="action-card text-center p-5 shadow-sm">
                                <div class="mb-3">
                                    <i class="bi bi-list-check fs-1" style="color: #a67c52;"></i>
                                </div>
                                <h4 class="fw-bold">Ver Pedidos</h4>
                                <p class="text-muted">Gestiona, edita o cancela los pedidos existentes.</p>
                                <a href="{{ route('pedidos.index') }}" class="btn btn-panaderia-action mt-2 px-4">
                                    Ver Listado
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</div>
{{-- ===================================================
     MODAL CREAR PEDIDO
     =================================================== --}}
<div class="modal fade" id="pedidoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 12px;">
            <div class="modal-header text-white" style="background: #a67c52; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title">Crear Nuevo Pedido</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="pedidoForm" method="POST" action="{{ route('pedidos.store') }}">
                @csrf
                <div class="modal-body row g-3">

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
                        <input type="number" class="form-control bg-light" id="modal_TOTAL_PRODUCTO" name="TOTAL_PRODUCTO" readonly>
                    </div>

                    {{-- Tabla de artículos --}}
                    <div class="col-12 mt-3">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                            <h6 class="fw-bold text-secondary mb-0">
                                <i class="fas fa-shopping-basket me-2"></i>Artículos del Pedido
                            </h6>
                            <button type="button" class="btn btn-sm btn-success" id="btn-agregar-fila">
                                <i class="fas fa-plus"></i> Añadir
                            </button>
                        </div>
                        <div class="table-responsive border rounded">
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
                                <tbody id="detalles-pedido-body">
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">Añada productos al pedido.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>{{-- /modal-body --}}
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white" style="background: #a67c52;">
                        <i class="fas fa-save me-1"></i>Guardar Pedido
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
/* =====================================================
   1. CARGA DE PRODUCTOS DESDE JAVA
   ===================================================== */
let listaProductosGlobal = [];

async function cargarProductosDesdeJava() {
    try {
        const response = await fetch('http://localhost:8080/productos');
        const data = await response.json();
        listaProductosGlobal = data.map(p => ({
            id:     p.id_PRODUCTO    || p.idProducto    || p["Id Producto:"],
            nombre: p.nombre_PRODUCTO|| p.nombreProducto|| p["Nombre Producto:"],
            precio: parseFloat(p.precio_UNITARIO || p.precio || p["Precio:"] || 0)
        }));
    } catch (error) {
        console.error("Error cargando productos:", error);
    }
}

/* =====================================================
   2. LÓGICA DEL MODAL
   ===================================================== */
document.addEventListener('DOMContentLoaded', function () {

    cargarProductosDesdeJava();

    const pedidoModal  = new bootstrap.Modal(document.getElementById('pedidoModal'));
    const detallesBody = document.getElementById('detalles-pedido-body');

    function agregarFila() {
        if (detallesBody.querySelector('td[colspan]')) {
            detallesBody.innerHTML = '';
        }

        const opciones = listaProductosGlobal.map(p =>
            `<option value="${p.id}" data-precio="${p.precio}">${p.nombre}</option>`
        ).join('');

        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td>
                <select name="productos[]" class="form-select form-select-sm select-producto" required>
                    <option value="">Seleccione...</option>
                    ${opciones}
                </select>
            </td>
            <td>
                <input type="number" name="cantidades[]"
                       class="form-control form-control-sm text-center input-cantidad"
                       value="1" min="1" required>
            </td>
            <td class="text-end align-middle">
                <span class="precio-unit text-muted">$0</span>
                <input type="hidden" name="precios_unitarios[]" class="input-precio-unitario" value="0">
            </td>
            <td class="text-end align-middle">
                <span class="subtotal-fila fw-bold">$0</span>
                <input type="hidden" name="subtotales[]" class="input-subtotal" value="0">
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
        let total = 0;
        detallesBody.querySelectorAll('.input-subtotal').forEach(i => {
            total += parseFloat(i.value) || 0;
        });
        document.getElementById('modal_TOTAL_PRODUCTO').value = total;
    }

    document.getElementById('btn-create-pedido').addEventListener('click', function () {
        document.getElementById('pedidoForm').reset();
        detallesBody.innerHTML = '<tr><td colspan="5" class="text-center py-3 text-muted">Añada productos al pedido.</td></tr>';
        pedidoModal.show();
    });

    document.getElementById('btn-agregar-fila').addEventListener('click', function (e) {
        e.preventDefault();
        agregarFila();
    });

    detallesBody.addEventListener('input', function (e) {
        if (e.target.classList.contains('select-producto') ||
            e.target.classList.contains('input-cantidad')) {

            const fila     = e.target.closest('tr');
            const select   = fila.querySelector('.select-producto');
            const cant     = parseInt(fila.querySelector('.input-cantidad').value) || 0;
            const precio   = parseFloat(select.options[select.selectedIndex]?.dataset.precio) || 0;
            const subtotal = precio * cant;

            fila.querySelector('.precio-unit').textContent         = `$${Number(precio).toLocaleString('es-CO')}`;
            fila.querySelector('.subtotal-fila').textContent       = `$${Number(subtotal).toLocaleString('es-CO')}`;
            fila.querySelector('.input-precio-unitario').value     = precio;
            fila.querySelector('.input-subtotal').value            = subtotal;

            actualizarTotalPedido();
        }
    });

    detallesBody.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-quitar');
        if (btn) {
            btn.closest('tr').remove();
            if (detallesBody.children.length === 0) {
                detallesBody.innerHTML = '<tr><td colspan="5" class="text-center py-3 text-muted">Añada productos al pedido.</td></tr>';
            }
            actualizarTotalPedido();
        }
    });

    /* =====================================================
       3. LÓGICA DE AUTENTICACIÓN / BIENVENIDA
       ===================================================== */
    if (typeof AuthManager !== 'undefined') {
        if (AuthManager.wasLoggedOut()) {
            AuthManager.clearAuth();
            window.location.replace('/login');
            return;
        }
        if (!AuthManager.isAuthenticated()) {
            AuthManager.redirectToLogin();
            return;
        }

        const userData = AuthManager.getUserData();
        const userRole = AuthManager.getRole();

        if (userRole !== 'EMPLEADO') {
            window.location.href = AuthManager.getDashboardRoute(userRole);
            return;
        }

        const welcomeTitle = document.getElementById('welcome-name');
        if (userData && (userData.nombre || userData.name)) {
            welcomeTitle.innerText = `¡Hola, ${userData.nombre || userData.name}!`;
        } else {
            welcomeTitle.innerText = '¡Bienvenido, Empleado!';
        }

        // Llenar datos del perfil
        const nombreCompleto = userData?.nombre || userData?.name  || 'No disponible';
        const emailUsuario   = userData?.email  || userData?.correo || 'No disponible';
        const rolUsuario     = userRole === 'EMPLEADO' ? 'Empleado' : (userRole ?? 'Empleado');

        const elNombre = document.getElementById('perfil-nombre');
        const elEmail  = document.getElementById('perfil-email');
        const elRol    = document.getElementById('perfil-rol');

        if (elNombre) elNombre.textContent = nombreCompleto;
        if (elEmail)  elEmail.textContent  = emailUsuario;
        if (elRol)    elRol.textContent    = rolUsuario;
    }

    /* =====================================================
       4. NAVEGACIÓN INTERNA DEL SIDEBAR
       ===================================================== */
    const navLinks      = document.querySelectorAll('.nav-link');
    const sections      = document.querySelectorAll('.section-content-inner');
    const dashboardMain = document.getElementById('main-actions');
    const statsMain     = document.getElementById('stats-row');

    navLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href && href.startsWith('#')) {
                e.preventDefault();
                navLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                sections.forEach(s => s.style.display = 'none');

                if (href === '#inicio') {
                    dashboardMain.style.display = 'block';
                    statsMain.style.display     = 'flex';
                } else {
                    dashboardMain.style.display = 'none';
                    statsMain.style.display     = 'none';
                    const target = document.getElementById(href.substring(1) + '-section');
                    if (target) target.style.display = 'block';
                }
            }
        });
    });
});
</script>
@endpush