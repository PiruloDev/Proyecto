@extends('layouts.app')

@section('title', 'Estadísticas - Panadería')

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
                <h1 class="h2">Estadísticas</h1>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

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

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
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

(function() {
    const canvas = document.getElementById('pieProductosMasVendidos');
    if (!canvas) return;

    const productosData = @json($productosMasVendidos ?? []);

    if (productosData.length === 0) return;

    const colores = [
        '#4a90e2', // Azul vibrante
        '#ffc107', // Amarillo dorado
        '#ff6b6b', // Rojo coral
        '#51cf66', // Verde menta
        '#ff9f43', // Naranja cálido
        '#a29bfe', // Lavanda
        '#fd79a8', // Rosa suave
        '#00d2d3', // Cian brillante
        '#fdcb6e', // Amarillo mostaza
        '#6c5ce7'  // Púrpura profundo
    ];

    const labels = productosData.map(p => p.nombreProducto || 'N/A');
    const valores = productosData.map(p => p.cantidadVendida || 0);
    const backgroundColors = productosData.map((_, i) => colores[i % colores.length]);

    // Asignar colores a los indicadores de la tabla
    productosData.forEach((_, index) => {
        const legendEl = document.getElementById('legend-color-' + index);
        if (legendEl) {
            legendEl.style.backgroundColor = colores[index % colores.length];
        }
    });

    new Chart(canvas, {
        type: 'pie',
        data: {
            labels: labels,
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
                legend: {
                    display: false
                },
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
</script>
@endsection
