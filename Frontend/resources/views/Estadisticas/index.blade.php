@extends('layouts.app')

@section('title', 'Estadísticas - Panadería')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="{{ asset('css/variables.css') }}" rel="stylesheet">
<link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet">
<link href="{{ asset('css/reportes-estadisticas.css') }}" rel="stylesheet">
<style>
    /* ── Sección usuarios registrados ── */
    .usuarios-section {
        margin-top: 2rem;
    }

    .usuarios-charts-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-top: 1rem;
    }

    @media (max-width: 768px) {
        .usuarios-charts-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Tarjetas de resumen */
    .usuarios-summary-cards {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
        flex-wrap: wrap;
    }

    .summary-card {
        flex: 1;
        min-width: 130px;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.85rem;
        color: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    }

    .summary-card.clientes  { background: linear-gradient(135deg, #4a90e2, #357abd); }
    .summary-card.empleados { background: linear-gradient(135deg, #51cf66, #37a34a); }
    .summary-card.admins    { background: linear-gradient(135deg, #a29bfe, #6c5ce7); }
    .summary-card.total     { background: linear-gradient(135deg, #ff9f43, #e17d20); }

    .summary-card .card-icon {
        font-size: 2rem;
        opacity: 0.9;
    }

    .summary-card .card-info .card-number {
        font-size: 1.6rem;
        font-weight: 700;
        line-height: 1;
    }

    .summary-card .card-info .card-label {
        font-size: 0.78rem;
        opacity: 0.88;
        margin-top: 2px;
    }

    /* Tabla de usuarios */
    .usuarios-table-wrapper {
        overflow-x: auto;
        margin-top: 1.25rem;
    }

    .usuarios-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .usuarios-table thead th {
        background: #f8f9fa;
        padding: 0.65rem 0.9rem;
        text-align: left;
        font-weight: 600;
        color: #555;
        border-bottom: 2px solid #e9ecef;
        white-space: nowrap;
    }

    .usuarios-table tbody tr {
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.15s;
    }

    .usuarios-table tbody tr:hover {
        background: #f8fbff;
    }

    .usuarios-table tbody td {
        padding: 0.6rem 0.9rem;
        color: #333;
        vertical-align: middle;
    }

    .rol-badge {
        display: inline-block;
        padding: 0.25rem 0.7rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .rol-badge.cliente    { background: #dbeafe; color: #1d4ed8; }
    .rol-badge.empleado   { background: #dcfce7; color: #166534; }
    .rol-badge.admin      { background: #ede9fe; color: #5b21b6; }

    .no-telefono {
        color: #aaa;
        font-style: italic;
        font-size: 0.8rem;
    }

    /* Estado vacío */
    .usuarios-empty {
        text-align: center;
        padding: 2.5rem 1rem;
        color: #888;
    }

    .usuarios-empty i {
        font-size: 2.5rem;
        display: block;
        margin-bottom: 0.5rem;
        opacity: 0.5;
    }

    /* Barra de búsqueda */
    .usuarios-filter-bar {
        display: flex;
        gap: 0.75rem;
        align-items: center;
        flex-wrap: wrap;
        margin-top: 1rem;
    }

    .usuarios-search {
        flex: 1;
        min-width: 200px;
        padding: 0.45rem 0.85rem;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        font-size: 0.875rem;
        outline: none;
        transition: border-color 0.2s;
    }

    .usuarios-search:focus {
        border-color: #4a90e2;
        box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.12);
    }

    .rol-filter-btn {
        padding: 0.4rem 0.9rem;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background: #fff;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.15s;
        color: #555;
    }

    .rol-filter-btn.active, .rol-filter-btn:hover {
        background: #4a90e2;
        color: #fff;
        border-color: #4a90e2;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Component -->
        @include('components.admin-sidebar')

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4 main-content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2">Estadísticas</h1>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- ═══════════════════════════════════════════════
                 SECCIÓN 1: PRODUCTOS MÁS VENDIDOS
            ═══════════════════════════════════════════════ --}}
            <div class="chart-section">
                <div class="chart-section-header">
                    <h3><i class="bi bi-pie-chart-fill me-2"></i>Productos Más Vendidos</h3>
                </div>

                @if(isset($productosMasVendidos) && count($productosMasVendidos) > 0)
                    <div class="chart-container">
                        <div class="chart-wrapper">
                            <canvas id="pieProductosMasVendidos"></canvas>
                        </div>
                        <div class="chart-legend-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Producto</th>
                                        <th>Categoría</th>
                                        <th>Precio</th>
                                        <th>Vendidos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($productosMasVendidos as $index => $producto)
                                    <tr>
                                        <td><span class="legend-color" id="legend-color-{{ $index }}"></span></td>
                                        <td>{{ $producto['nombreProducto'] ?? 'N/A' }}</td>
                                        <td>{{ $producto['categoriaProducto'] ?? 'Sin categoría' }}</td>
                                        <td>${{ number_format($producto['precioProducto'] ?? 0, 0, ',', '.') }}</td>
                                        <td><strong>{{ $producto['cantidadVendida'] ?? 0 }}</strong></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="chart-empty-state">
                        <i class="bi bi-bar-chart-line"></i>
                        <p>No hay datos de ventas disponibles para mostrar el gráfico.</p>
                        <small class="text-muted">Asegúrate de que la API de Spring Boot esté activa.</small>
                    </div>
                @endif
            </div>

            {{-- ═══════════════════════════════════════════════
                 SECCIÓN 2: USUARIOS REGISTRADOS
                 Datos: $usuariosRegistrados (List<UsuariosRegistradosDTO>)
                 Campos: nombre, email, telefono, rol (Cliente/Empleado/Administrador)
            ═══════════════════════════════════════════════ --}}
            <div class="chart-section usuarios-section">
                <div class="chart-section-header">
                    <h3><i class="bi bi-people-fill me-2"></i>Usuarios Registrados</h3>
                </div>

                @php
                    $usuarios    = $usuariosRegistrados ?? [];
                    $clientes    = collect($usuarios)->where('rol', 'Cliente')->count();
                    $empleados   = collect($usuarios)->where('rol', 'Empleado')->count();
                    $admins      = collect($usuarios)->where('rol', 'Administrador')->count();
                    $totalUsers  = count($usuarios);
                @endphp

                @if($totalUsers > 0)

                    {{-- Tarjetas de resumen --}}
                    <div class="usuarios-summary-cards">
                        <div class="summary-card total">
                            <i class="bi bi-people card-icon"></i>
                            <div class="card-info">
                                <div class="card-number">{{ $totalUsers }}</div>
                                <div class="card-label">Total usuarios</div>
                            </div>
                        </div>
                        <div class="summary-card clientes">
                            <i class="bi bi-person card-icon"></i>
                            <div class="card-info">
                                <div class="card-number">{{ $clientes }}</div>
                                <div class="card-label">Clientes</div>
                            </div>
                        </div>
                        <div class="summary-card empleados">
                            <i class="bi bi-person-badge card-icon"></i>
                            <div class="card-info">
                                <div class="card-number">{{ $empleados }}</div>
                                <div class="card-label">Empleados</div>
                            </div>
                        </div>
                        <div class="summary-card admins">
                            <i class="bi bi-shield-person card-icon"></i>
                            <div class="card-info">
                                <div class="card-number">{{ $admins }}</div>
                                <div class="card-label">Administradores</div>
                            </div>
                        </div>
                    </div>

                    {{-- Gráficos: donut de distribución + barras horizontales --}}
                    <div class="usuarios-charts-grid">
                        <div>
                            <canvas id="donutUsuarios" height="220"></canvas>
                        </div>
                        <div>
                            <canvas id="barUsuarios" height="220"></canvas>
                        </div>
                    </div>

                    {{-- Barra de búsqueda y filtros --}}
                    <div class="usuarios-filter-bar">
                        <input
                            type="text"
                            class="usuarios-search"
                            id="usuariosSearch"
                            placeholder="Buscar por nombre o email..."
                        >
                        <button class="rol-filter-btn active" data-rol="todos">Todos</button>
                        <button class="rol-filter-btn" data-rol="Cliente">Clientes</button>
                        <button class="rol-filter-btn" data-rol="Empleado">Empleados</button>
                        <button class="rol-filter-btn" data-rol="Administrador">Administradores</button>
                    </div>

                    {{-- Tabla de usuarios --}}
                    <div class="usuarios-table-wrapper">
                        <table class="usuarios-table" id="usuariosTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Rol</th>
                                </tr>
                            </thead>
                            <tbody id="usuariosTableBody">
                                @foreach($usuarios as $i => $usuario)
                                <tr
                                    data-rol="{{ $usuario['rol'] ?? '' }}"
                                    data-nombre="{{ strtolower($usuario['nombre'] ?? '') }}"
                                    data-email="{{ strtolower($usuario['email'] ?? '') }}"
                                >
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $usuario['nombre'] ?? 'N/A' }}</td>
                                    <td>{{ $usuario['email'] ?? 'N/A' }}</td>
                                    <td>
                                        @if(!empty($usuario['telefono']))
                                            {{ $usuario['telefono'] }}
                                        @else
                                            <span class="no-telefono">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $rol = $usuario['rol'] ?? '';
                                            $badgeClass = match($rol) {
                                                'Cliente'       => 'cliente',
                                                'Empleado'      => 'empleado',
                                                'Administrador' => 'admin',
                                                default         => ''
                                            };
                                        @endphp
                                        <span class="rol-badge {{ $badgeClass }}">{{ $rol }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                @else
                    <div class="usuarios-empty">
                        <i class="bi bi-person-x"></i>
                        <p>No hay usuarios registrados disponibles.</p>
                        <small class="text-muted">Asegúrate de que la API de Spring Boot esté activa en <code>/reporte/usuarios</code>.</small>
                    </div>
                @endif
            </div>
            {{-- FIN SECCIÓN USUARIOS --}}

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
// ── Actualizar nombre y rol del administrador en sidebar ──
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

// ── Gráfico Pie: Productos más vendidos ──
(function() {
    const canvas = document.getElementById('pieProductosMasVendidos');
    if (!canvas) return;

    const productosData = @json($productosMasVendidos ?? []);
    if (productosData.length === 0) return;

    const colores = [
        '#4a90e2','#ffc107','#ff6b6b','#51cf66','#ff9f43',
        '#a29bfe','#fd79a8','#00d2d3','#fdcb6e','#6c5ce7'
    ];

    const labels           = productosData.map(p => p.nombreProducto || 'N/A');
    const valores          = productosData.map(p => p.cantidadVendida || 0);
    const backgroundColors = productosData.map((_, i) => colores[i % colores.length]);

    productosData.forEach((_, index) => {
        const legendEl = document.getElementById('legend-color-' + index);
        if (legendEl) legendEl.style.backgroundColor = colores[index % colores.length];
    });

    new Chart(canvas, {
        type: 'pie',
        data: {
            labels,
            datasets: [{
                data: valores,
                backgroundColor: backgroundColors,
                borderColor: '#ffffff',
                borderWidth: 2,
                hoverBorderWidth: 3,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(44, 62, 80, 0.95)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#4a90e2',
                    borderWidth: 2,
                    cornerRadius: 12,
                    padding: 16,
                    titleFont: { size: 14, weight: 'bold', family: 'Quicksand' },
                    bodyFont: { size: 13, family: 'Quicksand' },
                    callbacks: {
                        label: function(context) {
                            const producto = productosData[context.dataIndex];
                            const total = valores.reduce((a, b) => a + b, 0);
                            const porcentaje = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                            return [
                                'Cantidad: ' + context.raw + ' unidades',
                                'Porcentaje: ' + porcentaje + '%',
                                'Precio: $' + Number(producto.precioProducto || 0).toLocaleString('es-CO'),
                                'Categoría: ' + (producto.categoriaProducto || 'N/A')
                            ];
                        }
                    }
                }
            }
        }
    });
})();

// ── Gráficos de Usuarios Registrados ──
(function() {
    // Los datos vienen del controller Laravel (ya los llamó a la API de Spring Boot)
    const usuariosData = @json($usuariosRegistrados ?? []);
    if (!usuariosData.length) return;

    // Contar por rol (coincide con los valores que devuelve UsuariosRegistradosService)
    const conteo = { Cliente: 0, Empleado: 0, Administrador: 0 };
    usuariosData.forEach(u => {
        if (conteo.hasOwnProperty(u.rol)) conteo[u.rol]++;
    });

    const coloresRoles = {
        Cliente:       '#4a90e2',
        Empleado:      '#51cf66',
        Administrador: '#a29bfe'
    };

    // ─── Donut: distribución por rol ───
    const donutCanvas = document.getElementById('donutUsuarios');
    if (donutCanvas) {
        new Chart(donutCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Clientes', 'Empleados', 'Administradores'],
                datasets: [{
                    data: [conteo.Cliente, conteo.Empleado, conteo.Administrador],
                    backgroundColor: [
                        coloresRoles.Cliente,
                        coloresRoles.Empleado,
                        coloresRoles.Administrador
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                cutout: '62%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 16,
                            font: { size: 12 },
                            usePointStyle: true,
                            pointStyleWidth: 10
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = usuariosData.length;
                                const pct = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                                return context.label + ': ' + context.raw + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    // ─── Barras horizontales: cantidad por rol ───
    const barCanvas = document.getElementById('barUsuarios');
    if (barCanvas) {
        new Chart(barCanvas, {
            type: 'bar',
            data: {
                labels: ['Clientes', 'Empleados', 'Administradores'],
                datasets: [{
                    label: 'Usuarios',
                    data: [conteo.Cliente, conteo.Empleado, conteo.Administrador],
                    backgroundColor: [
                        coloresRoles.Cliente + 'cc',
                        coloresRoles.Empleado + 'cc',
                        coloresRoles.Administrador + 'cc'
                    ],
                    borderColor: [
                        coloresRoles.Cliente,
                        coloresRoles.Empleado,
                        coloresRoles.Administrador
                    ],
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' ' + ctx.raw + ' usuarios'
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    y: {
                        grid: { display: false }
                    }
                }
            }
        });
    }
})();

// ── Filtros y búsqueda en la tabla de usuarios ──
(function() {
    const searchInput  = document.getElementById('usuariosSearch');
    const filterBtns   = document.querySelectorAll('.rol-filter-btn');
    const rows         = document.querySelectorAll('#usuariosTableBody tr');

    let activeRol = 'todos';

    function filterRows() {
        const term = searchInput ? searchInput.value.toLowerCase().trim() : '';
        rows.forEach(row => {
            const rol    = row.dataset.rol    || '';
            const nombre = row.dataset.nombre || '';
            const email  = row.dataset.email  || '';

            const matchRol  = activeRol === 'todos' || rol === activeRol;
            const matchTerm = !term || nombre.includes(term) || email.includes(term);

            row.style.display = matchRol && matchTerm ? '' : 'none';
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterRows);
    }

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            activeRol = btn.dataset.rol;
            filterRows();
        });
    });
})();
</script>
@endsection