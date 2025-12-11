@extends('layouts.app')

@section('title', 'Gestión de Productos - Panadería')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="{{ asset('css/variables.css') }}" rel="stylesheet">

<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
    }
    .producto-img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }
    .btn-action {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    .table-actions {
        white-space: nowrap;
    }
    .search-section {
        background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- HEADER -->
    <div class="search-section d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="h2 mb-2"><i class="bi bi-box-seam"></i> Gestión de Productos</h1>
            <p class="mb-0 opacity-75">Administra el catálogo de productos de tu panadería</p>
        </div>
        <button class="btn btn-light btn-lg" data-bs-toggle="modal" data-bs-target="#modalProducto" onclick="limpiarFormulario()">
            <i class="bi bi-plus-circle"></i> Nuevo Producto
        </button>
    </div>

    <!-- FILTROS -->
    <div class="card glass-card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="buscarProducto" class="form-control" placeholder="Buscar por nombre...">
                    </div>
                </div>

                <div class="col-md-3">
                    <select id="filtroCategoria" class="form-select">
                        <option value="">📦 Todas las categorías</option>
                        <option value="panes">🍞 Panes</option>
                        <option value="pasteles">🎂 Pasteles</option>
                        <option value="galletas">🍪 Galletas</option>
                        <option value="bebidas">☕ Bebidas</option>
                        <option value="postres">🍰 Postres</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select id="filtroEstado" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button onclick="limpiarFiltros()" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-clockwise"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card glass-card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-box-seam fs-2 text-primary"></i>
                    <h3 class="mt-2 mb-0" id="totalProductos">{{ isset($productos) ? count($productos) : 0 }}</h3>
                    <small class="text-muted">Total Productos</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card glass-card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle fs-2 text-success"></i>
                    {{-- No tienes campo ACTIVO en el JSON: asumimos activo por defecto --}}
                    <h3 class="mt-2 mb-0" id="productosActivos">{{ isset($productos) ? count($productos) : 0 }}</h3>
                    <small class="text-muted">Productos Activos</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card glass-card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-exclamation-triangle fs-2 text-warning"></i>
                    <h3 class="mt-2 mb-0" id="stockBajo">
                        {{ isset($productos) ? collect($productos)->filter(function($p){ return isset($p['Stock Minímo:']) && (int)$p['Stock Minímo:'] <= 10 && (int)$p['Stock Minímo:'] > 0; })->count() : 0 }}
                    </h3>
                    <small class="text-muted">Stock Bajo</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card glass-card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-cash-coin fs-2 text-info"></i>
                    <h3 class="mt-2 mb-0" id="valorInventario">
                        ${{ isset($productos) ? number_format( collect($productos)->sum(function($p){ return (float)($p['Precio:'] ?? 0) * ((int)($p['Stock Minímo:'] ?? 0)); }), 0, ',', '.') : 0 }}
                    </h3>
                    <small class="text-muted">Valor Inventario</small>
                </div>
            </div>
        </div>
    </div>

    <!-- TABLA -->
    <div class="card glass-card border-0 shadow-sm">
        <div class="card-header bg-white border-0">
            <h5 class="mb-0"><i class="bi bi-list-ul"></i> Listado de Productos</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Categoría (ID)</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Marca</th>
                            <th class="text-center pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaProductos">
                        @if(isset($productos) && count($productos) > 0)
                            @foreach($productos as $producto)
                                <tr
                                    data-id="{{ $producto['Id Producto:'] ?? '' }}"
                                    data-nombre="{{ $producto['Nombre Producto:'] ?? '' }}"
                                    data-categoria="{{ $producto['Id Categoria Producto:'] ?? '' }}"
                                    data-estado="{{ isset($producto['ACTIVO']) ? ($producto['ACTIVO'] ? 'activo' : 'inactivo') : 'activo' }}"
                                >
                                    <td class="ps-4"><strong>#{{ $producto['Id Producto:'] ?? '' }}</strong></td>

                                    <td>
                                        {{-- No hay campo imagen en tu JSON: mostramos placeholder --}}
                                        <img src="https://via.placeholder.com/60" alt="{{ $producto['Nombre Producto:'] ?? '' }}" class="producto-img">
                                    </td>

                                    <td><strong>{{ $producto['Nombre Producto:'] ?? '-' }}</strong></td>

                                    <td>
                                        <span class="badge bg-info text-dark">
                                            {{ $producto['Id Categoria Producto:'] ?? '-' }}
                                        </span>
                                    </td>

                                    <td><small>{{ Str::limit($producto['Descripcion Producto:'] ?? ($producto['Descripcion'] ?? ''), 50) }}</small></td>

                                    <td><strong>${{ number_format((float)($producto['Precio:'] ?? 0), 0, ',', '.') }}</strong></td>

                                    <td>
                                        @php $stockVal = isset($producto['Stock Minímo:']) ? (int)$producto['Stock Minímo:'] : 0; @endphp
                                        @if($stockVal <= 10 && $stockVal > 0)
                                            <span class="badge bg-warning text-dark">{{ $stockVal }}</span>
                                        @elseif($stockVal == 0)
                                            <span class="badge bg-danger">{{ $stockVal }}</span>
                                        @else
                                            <span class="badge bg-success">{{ $stockVal }}</span>
                                        @endif
                                    </td>

                                    <td>{{ $producto['Marca Producto:'] ?? '-' }}</td>

                                    <td class="text-center table-actions pe-4">
                                        <button class="btn btn-sm btn-info btn-action me-1" onclick="verDetalles('{{ $producto['Id Producto:'] ?? '' }}')" title="Ver detalles">
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        <button class="btn btn-sm btn-warning btn-action me-1" onclick='editarProducto(@json($producto))' title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <button class="btn btn-sm btn-danger btn-action" onclick="eliminarProducto('{{ $producto['Id Producto:'] ?? '' }}', '{{ addslashes($producto['Nombre Producto:'] ?? '') }}')" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 text-muted"></i>
                                    <p class="text-muted mt-2">No hay productos registrados</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Botón volver -->
    <div class="mt-4">
        <a href="{{ route('dashboard.admin') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver al Dashboard
        </a>
    </div>
</div>

{{-- MODAL CREAR/EDITAR --}}
<div class="modal fade" id="modalProducto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formProducto" method="POST">
                @csrf
                <input type="hidden" name="_method" id="methodField" value="POST">

                <div class="modal-header" style="background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%); color:white;">
                    <h5 class="modal-title"><i class="bi bi-box-seam"></i> Producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="nombreProducto" class="form-label"><i class="bi bi-tag"></i> Nombre del Producto *</label>
                            <input type="text" class="form-control" id="nombreProducto" name="nombre" required>
                        </div>

                        <div class="col-md-4">
                            <label for="estadoProducto" class="form-label"><i class="bi bi-toggle-on"></i> Estado *</label>
                            <select id="estadoProducto" name="estado" class="form-select" required>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="categoriaProducto" class="form-label"><i class="bi bi-grid"></i> Categoría *</label>
                            <select id="categoriaProducto" name="categoria" class="form-select" required>
                                <option value="">Seleccione una categoría</option>
                                <option value="panes">🍞 Panes</option>
                                <option value="pasteles">🎂 Pasteles</option>
                                <option value="galletas">🍪 Galletas</option>
                                <option value="bebidas">☕ Bebidas</option>
                                <option value="postres">🍰 Postres</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="precioProducto" class="form-label"><i class="bi bi-currency-dollar"></i> Precio (COP) *</label>
                            <input type="number" step="0.01" class="form-control" id="precioProducto" name="precio" min="0" required>
                        </div>

                        <div class="col-md-3">
                            <label for="stockProducto" class="form-label"><i class="bi bi-box"></i> Stock *</label>
                            <input type="number" class="form-control" id="stockProducto" name="stock" min="0" required>
                        </div>

                        <div class="col-md-12">
                            <label for="descripcionProducto" class="form-label"><i class="bi bi-file-text"></i> Descripción</label>
                            <textarea class="form-control" id="descripcionProducto" name="descripcion" rows="3"></textarea>
                        </div>

                        <div class="col-md-12">
                            <label for="imagenProducto" class="form-label"><i class="bi bi-image"></i> URL de Imagen</label>
                            <input type="url" class="form-control" id="imagenProducto" name="imagen" placeholder="https://ejemplo.com/imagen.jpg">
                            <small class="text-muted">Ingresa la URL de una imagen del producto (opcional)</small>
                        </div>

                        <div class="col-md-12" id="vistaPrevia" style="display:none;">
                            <label class="form-label">Vista Previa:</label>
                            <div class="text-center">
                                <img id="imagenPreview" src="" alt="Vista previa" class="img-fluid rounded" style="max-height:200px;">
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary" style="background:#8B4513; border: none;">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const productosData = @json($productos ?? []);

    document.addEventListener('DOMContentLoaded', function() {
        const buscarInput = document.getElementById('buscarProducto');
        if (buscarInput) buscarInput.addEventListener('input', filtrarProductos);

        const filtroCategoria = document.getElementById('filtroCategoria');
        if (filtroCategoria) filtroCategoria.addEventListener('change', filtrarProductos);

        const filtroEstado = document.getElementById('filtroEstado');
        if (filtroEstado) filtroEstado.addEventListener('change', filtrarProductos);

        const imagenInput = document.getElementById('imagenProducto');
        if (imagenInput) {
            imagenInput.addEventListener('input', function() {
                const url = this.value;
                const preview = document.getElementById('imagenPreview');
                const cont = document.getElementById('vistaPrevia');
                if (url && url.trim() !== '') {
                    preview.src = url;
                    cont.style.display = 'block';
                } else {
                    cont.style.display = 'none';
                }
            });
        }
    });

    function filtrarProductos() {
        const busqueda = (document.getElementById('buscarProducto').value || '').toLowerCase();
        const categoria = document.getElementById('filtroCategoria').value;
        const estado = document.getElementById('filtroEstado').value;

        const filas = document.querySelectorAll('#tablaProductos tr[data-id]');
        filas.forEach(fila => {
            const nombre = (fila.getAttribute('data-nombre') || '').toLowerCase();
            const categoriaProducto = fila.getAttribute('data-categoria') || '';
            const estadoProducto = fila.getAttribute('data-estado') || '';

            const cumpleBusqueda = nombre.includes(busqueda);
            const cumpleCategoria = !categoria || categoriaProducto === categoria;
            const cumpleEstado = !estado || estadoProducto === estado;

            fila.style.display = (cumpleBusqueda && cumpleCategoria && cumpleEstado) ? '' : 'none';
        });
    }

    function limpiarFormulario() {
        const form = document.getElementById('formProducto');
        if (form) form.reset();
        document.getElementById('methodField').value = 'POST';
        document.getElementById('productoId').value = '';
        const vista = document.getElementById('vistaPrevia');
        if (vista) vista.style.display = 'none';
        document.getElementById('modalProductoLabel').innerHTML = '<i class="bi bi-box-seam"></i> Nuevo Producto';
    }

   function editarProducto(p) {
    document.querySelector("#modalProducto .modal-title").innerHTML = "Editar Producto";

    document.getElementById("methodField").value = "PUT"; 

    document.getElementById("formProducto").action = "/productos/" + p["Id Producto:"];

    document.getElementById("nombreProducto").value = p["Nombre Producto:"] ?? '';
    document.getElementById("estadoProducto").value = (p["ACTIVO"] ?? true) ? "activo" : "inactivo";
    document.getElementById("categoriaProducto").value = p["Id Categoria Producto:"] ?? '';
    document.getElementById("precioProducto").value = p["Precio:"] ?? 0;
    document.getElementById("stockProducto").value = p["Stock Minímo:"] ?? 0;
    document.getElementById("descripcionProducto").value = p["Descripcion Producto:"] ?? '';

    if (p["Imagen Producto:"]) {
        document.getElementById("imagenProducto").value = p["Imagen Producto:"];
        document.getElementById("imagenPreview").src = p["Imagen Producto:"];
        document.getElementById("vistaPrevia").style.display = "block";
    } else {
        document.getElementById("vistaPrevia").style.display = "none";
    }

    var modal = new bootstrap.Modal(document.getElementById('modalProducto'));
    modal.show();
}



    function eliminarProducto(id, nombre) {
        if (!confirm(`¿Está seguro de eliminar el producto "${nombre}"?`)) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/productos/${id}`;
        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = '{{ csrf_token() }}';
        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'DELETE';
        form.appendChild(token);
        form.appendChild(method);
        document.body.appendChild(form);
        form.submit();
    }

    function verDetalles(id) {
        const producto = productosData.find(p => String(p['Id Producto:']) === String(id));
        if (!producto) return alert('Producto no encontrado');
        const cont = document.getElementById('detalleProductoContenido');
        if (!cont) {
            // si no hay modal de detalles, mostramos un alert simple
            alert(`${producto['Nombre Producto:']}\nPrecio: ${producto['Precio:']}\nStock: ${producto['Stock Minímo:']}`);
            return;
        }
        cont.innerHTML = `
            <div class="text-center mb-3">
                <img src="https://via.placeholder.com/250" alt="${producto['Nombre Producto:']}" class="img-fluid rounded" style="max-height:250px;">
            </div>
            <h4>${producto['Nombre Producto:']}</h4>
            <hr>
            <p><strong>ID:</strong> #${producto['Id Producto:']}</p>
            <p><strong>Categoría (ID):</strong> ${producto['Id Categoria Producto:'] ?? '-'}</p>
            <p><strong>Precio:</strong> $${new Intl.NumberFormat('es-CO').format(parseFloat(producto['Precio:'] || 0))} COP</p>
            <p><strong>Stock:</strong> ${producto['Stock Minímo:'] ?? '0'} unidades</p>
            <p><strong>Marca:</strong> ${producto['Marca Producto:'] ?? '-'}</p>
            <p><strong>Fecha Vencimiento:</strong> ${producto['Fecha Vencimiento:'] ?? '-'}</p>
        `;
        var modal = new bootstrap.Modal(document.getElementById('modalDetalleProducto'));
        modal.show();
    }

    function limpiarFiltros() {
        document.getElementById('buscarProducto').value = '';
        document.getElementById('filtroCategoria').value = '';
        document.getElementById('filtroEstado').value = '';
        filtrarProductos();
    }

    function limpiarFormulario() {

    document.getElementById("formProducto").reset();

    // Método vuelve a POST
    document.getElementById("methodField").value = "POST";

    // Action vuelve a productos.store
    document.getElementById("formProducto").action = "/productos";

    // Ocultar vista previa
    document.getElementById("vistaPrevia").style.display = "none";
}

</script>
@endpush
