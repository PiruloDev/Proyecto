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
    <!-- Header -->
    <div class="search-section">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1 class="h2 mb-2">
                    <i class="bi bi-box-seam"></i> Gestión de Productos
                </h1>
                <p class="mb-0 opacity-75">Administra el catálogo de productos de tu panadería</p>
            </div>
            <button class="btn btn-light btn-lg" data-bs-toggle="modal" data-bs-target="#modalProducto" onclick="limpiarFormulario()">
                <i class="bi bi-plus-circle"></i> Nuevo Producto
            </button>
        </div>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="card glass-card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" id="buscarProducto" placeholder="Buscar por nombre...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filtroCategoria">
                        <option value="">📦 Todas las categorías</option>
                        <option value="panes">🍞 Panes</option>
                        <option value="pasteles">🎂 Pasteles</option>
                        <option value="galletas">🍪 Galletas</option>
                        <option value="bebidas">☕ Bebidas</option>
                        <option value="postres">🍰 Postres</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filtroEstado">
                        <option value="">Todos los estados</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" onclick="limpiarFiltros()">
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
                    <h3 class="mt-2 mb-0" id="totalProductos">0</h3>
                    <small class="text-muted">Total Productos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card glass-card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle fs-2 text-success"></i>
                    <h3 class="mt-2 mb-0" id="productosActivos">0</h3>
                    <small class="text-muted">Productos Activos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card glass-card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-exclamation-triangle fs-2 text-warning"></i>
                    <h3 class="mt-2 mb-0" id="stockBajo">0</h3>
                    <small class="text-muted">Stock Bajo</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card glass-card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-cash-coin fs-2 text-info"></i>
                    <h3 class="mt-2 mb-0" id="valorInventario">$0</h3>
                    <small class="text-muted">Valor Inventario</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de productos -->
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
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Estado</th>
                            <th class="text-center pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaProductos">
                        <!-- Los productos se cargarán aquí dinámicamente -->
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

<!-- Modal para Crear/Editar Producto -->
<div class="modal fade" id="modalProducto" tabindex="-1" aria-labelledby="modalProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%); color: white;">
                <h5 class="modal-title" id="modalProductoLabel">
                    <i class="bi bi-box-seam"></i> Nuevo Producto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formProducto">
                    <input type="hidden" id="productoId">
                    
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="nombreProducto" class="form-label">
                                <i class="bi bi-tag"></i> Nombre del Producto *
                            </label>
                            <input type="text" class="form-control" id="nombreProducto" required placeholder="Ej: Pan Francés">
                        </div>
                        <div class="col-md-4">
                            <label for="estadoProducto" class="form-label">
                                <i class="bi bi-toggle-on"></i> Estado *
                            </label>
                            <select class="form-select" id="estadoProducto" required>
                                <option value="activo"> Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="categoriaProducto" class="form-label">
                                <i class="bi bi-grid"></i> Categoría *
                            </label>
                            <select class="form-select" id="categoriaProducto" required>
                                <option value="">Seleccione una categoría</option>
                                <option value="panes">🍞 Panes</option>
                                <option value="pasteles">🎂 Pasteles</option>
                                <option value="galletas">🍪 Galletas</option>
                                <option value="bebidas">☕ Bebidas</option>
                                <option value="postres">🍰 Postres</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="precioProducto" class="form-label">
                                <i class="bi bi-currency-dollar"></i> Precio (COP) *
                            </label>
                            <input type="number" class="form-control" id="precioProducto" step="0.01" min="0" required placeholder="0.00">
                        </div>

                        <div class="col-md-3">
                            <label for="stockProducto" class="form-label">
                                <i class="bi bi-box"></i> Stock *
                            </label>
                            <input type="number" class="form-control" id="stockProducto" min="0" required placeholder="0">
                        </div>

                        <div class="col-md-12">
                            <label for="descripcionProducto" class="form-label">
                                <i class="bi bi-file-text"></i> Descripción
                            </label>
                            <textarea class="form-control" id="descripcionProducto" rows="3" placeholder="Describe el producto..."></textarea>
                        </div>

                        <div class="col-md-12">
                            <label for="imagenProducto" class="form-label">
                                <i class="bi bi-image"></i> URL de Imagen
                            </label>
                            <input type="url" class="form-control" id="imagenProducto" placeholder="https://ejemplo.com/imagen.jpg">
                            <small class="text-muted">Ingresa la URL de una imagen del producto</small>
                        </div>

                        <div class="col-md-12" id="vistaPrevia" style="display: none;">
                            <label class="form-label">Vista Previa:</label>
                            <div class="text-center">
                                <img id="imagenPreview" src="" alt="Vista previa" class="img-fluid rounded" style="max-height: 200px;">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="guardarProducto()" style="background: #8B4513; border: none;">
                    <i class="bi bi-save"></i> Guardar Producto
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalles del Producto -->
<div class="modal fade" id="modalDetalleProducto" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-info-circle"></i> Detalles del Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalleProductoContenido">
                <!-- Se llenará dinámicamente -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Datos de ejemplo (en producción vendrían del backend)
    let productos = @json($productos ?? []);

    document.addEventListener('DOMContentLoaded', function() {
        cargarProductos();
        actualizarEstadisticas();

        // Event listeners para filtros
        document.getElementById('buscarProducto').addEventListener('input', filtrarProductos);
        document.getElementById('filtroCategoria').addEventListener('change', filtrarProductos);
        document.getElementById('filtroEstado').addEventListener('change', filtrarProductos);

        // Vista previa de imagen
        document.getElementById('imagenProducto').addEventListener('input', function() {
            const url = this.value;
            const preview = document.getElementById('vistaPrevia');
            const img = document.getElementById('imagenPreview');
            
            if (url) {
                img.src = url;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        });
    });

    function cargarProductos() {
        const tbody = document.getElementById('tablaProductos');
        tbody.innerHTML = '';
        
        if (productos.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <p class="text-muted mt-2">No hay productos registrados</p>
                    </td>
                </tr>
            `;
            return;
        }

        productos.forEach(producto => {
            const tr = document.createElement('tr');
            const descripcionCorta = producto.descripcion.length > 50 
                ? producto.descripcion.substring(0, 50) + '...' 
                : producto.descripcion;
            
            tr.innerHTML = `
                <td class="ps-4"><strong>#${producto.id}</strong></td>
                <td>
                    <img src="${producto.imagen}" alt="${producto.nombre}" class="producto-img" 
                         onerror="this.src='https://via.placeholder.com/60'">
                </td>
                <td><strong>${producto.nombre}</strong></td>
                <td>
                    <span class="badge bg-info text-dark">
                        ${getCategoriaEmoji(producto.categoria)} ${capitalizar(producto.categoria)}
                    </span>
                </td>
                <td><small>${descripcionCorta}</small></td>
                <td><strong>$${formatearPrecio(producto.precio)}</strong></td>
                <td>
                    ${producto.stock <= 10 && producto.stock > 0 
                        ? `<span class="badge bg-warning text-dark">${producto.stock}</span>` 
                        : producto.stock === 0 
                        ? `<span class="badge bg-danger">${producto.stock}</span>`
                        : `<span class="badge bg-success">${producto.stock}</span>`
                    }
                </td>
                <td>
                    <span class="badge ${producto.estado === 'activo' ? 'bg-success' : 'bg-danger'}">
                        ${producto.estado === 'activo' ? 'Activo' : 'Inactivo'}
                    </span>
                </td>
                <td class="text-center table-actions pe-4">
                    <button class="btn btn-sm btn-info btn-action me-1" onclick="verDetalles(${producto.id})" title="Ver detalles">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning btn-action me-1" onclick="editarProducto(${producto.id})" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-danger btn-action" onclick="eliminarProducto(${producto.id})" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function filtrarProductos() {
        const busqueda = document.getElementById('buscarProducto').value.toLowerCase();
        const categoria = document.getElementById('filtroCategoria').value;
        const estado = document.getElementById('filtroEstado').value;
        
        const productosFiltrados = productos.filter(producto => {
            const cumpleBusqueda = producto.nombre.toLowerCase().includes(busqueda) || 
                                   producto.descripcion.toLowerCase().includes(busqueda);
            const cumpleCategoria = !categoria || producto.categoria === categoria;
            const cumpleEstado = !estado || producto.estado === estado;
            return cumpleBusqueda && cumpleCategoria && cumpleEstado;
        });
        
        const tbody = document.getElementById('tablaProductos');
        tbody.innerHTML = '';
        
        productosFiltrados.forEach(producto => {
            const tr = document.createElement('tr');
            const descripcionCorta = producto.descripcion.length > 50 
                ? producto.descripcion.substring(0, 50) + '...' 
                : producto.descripcion;
            
            tr.innerHTML = `
                <td class="ps-4"><strong>#${producto.id}</strong></td>
                <td>
                    <img src="${producto.imagen}" alt="${producto.nombre}" class="producto-img"
                         onerror="this.src='https://via.placeholder.com/60'">
                </td>
                <td><strong>${producto.nombre}</strong></td>
                <td>
                    <span class="badge bg-info text-dark">
                        ${getCategoriaEmoji(producto.categoria)} ${capitalizar(producto.categoria)}
                    </span>
                </td>
                <td><small>${descripcionCorta}</small></td>
                <td><strong>$${formatearPrecio(producto.precio)}</strong></td>
                <td>
                    ${producto.stock <= 10 && producto.stock > 0 
                        ? `<span class="badge bg-warning text-dark">${producto.stock}</span>` 
                        : producto.stock === 0 
                        ? `<span class="badge bg-danger">${producto.stock}</span>`
                        : `<span class="badge bg-success">${producto.stock}</span>`
                    }
                </td>
                <td>
                    <span class="badge ${producto.estado === 'activo' ? 'bg-success' : 'bg-danger'}">
                        ${producto.estado === 'activo' ? 'Activo' : 'Inactivo'}
                    </span>
                </td>
                <td class="text-center table-actions pe-4">
                    <button class="btn btn-sm btn-info btn-action me-1" onclick="verDetalles(${producto.id})" title="Ver detalles">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning btn-action me-1" onclick="editarProducto(${producto.id})" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-danger btn-action" onclick="eliminarProducto(${producto.id})" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function limpiarFormulario() {
        document.getElementById('formProducto').reset();
        document.getElementById('productoId').value = '';
        document.getElementById('modalProductoLabel').innerHTML = '<i class="bi bi-box-seam"></i> Nuevo Producto';
        document.getElementById('vistaPrevia').style.display = 'none';
    }

    function guardarProducto() {
        const id = document.getElementById('productoId').value;
        const producto = {
            id: id ? parseInt(id) : Math.max(...productos.map(p => p.id)) + 1,
            nombre: document.getElementById('nombreProducto').value,
            categoria: document.getElementById('categoriaProducto').value,
            descripcion: document.getElementById('descripcionProducto').value || 'Sin descripción',
            precio: parseFloat(document.getElementById('precioProducto').value),
            stock: parseInt(document.getElementById('stockProducto').value),
            estado: document.getElementById('estadoProducto').value,
            imagen: document.getElementById('imagenProducto').value || 'https://via.placeholder.com/60'
        };
        
        if (id) {
            const index = productos.findIndex(p => p.id === parseInt(id));
            productos[index] = producto;
            mostrarAlerta('Producto actualizado correctamente', 'success');
        } else {
            productos.push(producto);
            mostrarAlerta('Producto creado correctamente', 'success');
        }
        
        cargarProductos();
        actualizarEstadisticas();
        bootstrap.Modal.getInstance(document.getElementById('modalProducto')).hide();
    }

    function editarProducto(id) {
        const producto = productos.find(p => p.id === id);
        if (producto) {
            document.getElementById('productoId').value = producto.id;
            document.getElementById('nombreProducto').value = producto.nombre;
            document.getElementById('categoriaProducto').value = producto.categoria;
            document.getElementById('descripcionProducto').value = producto.descripcion;
            document.getElementById('precioProducto').value = producto.precio;
            document.getElementById('stockProducto').value = producto.stock;
            document.getElementById('estadoProducto').value = producto.estado;
            document.getElementById('imagenProducto').value = producto.imagen;
            document.getElementById('modalProductoLabel').innerHTML = '<i class="bi bi-pencil"></i> Editar Producto';
            
            if (producto.imagen) {
                document.getElementById('imagenPreview').src = producto.imagen;
                document.getElementById('vistaPrevia').style.display = 'block';
            }
            
            const modal = new bootstrap.Modal(document.getElementById('modalProducto'));
            modal.show();
        }
    }

    function eliminarProducto(id) {
        const producto = productos.find(p => p.id === id);
        if (confirm(`¿Está seguro de eliminar el producto "${producto.nombre}"?`)) {
            productos = productos.filter(p => p.id !== id);
            cargarProductos();
            actualizarEstadisticas();
            mostrarAlerta('Producto eliminado correctamente', 'danger');
        }
    }

    function verDetalles(id) {
        const producto = productos.find(p => p.id === id);
        if (producto) {
            const contenido = document.getElementById('detalleProductoContenido');
            contenido.innerHTML = `
                <div class="text-center mb-3">
                    <img src="${producto.imagen}" alt="${producto.nombre}" class="img-fluid rounded" style="max-height: 250px;">
                </div>
                <h4>${producto.nombre}</h4>
                <hr>
                <p><strong>ID:</strong> #${producto.id}</p>
                <p><strong>Categoría:</strong> <span class="badge bg-info">${getCategoriaEmoji(producto.categoria)} ${capitalizar(producto.categoria)}</span></p>
                <p><strong>Precio:</strong> $${formatearPrecio(producto.precio)} COP</p>
                <p><strong>Stock:</strong> ${producto.stock} unidades</p>
                <p><strong>Estado:</strong> <span class="badge ${producto.estado === 'activo' ? 'bg-success' : 'bg-danger'}">${producto.estado === 'activo' ? '✅ Activo' : '❌ Inactivo'}</span></p>
                <p><strong>Descripción:</strong><br>${producto.descripcion}</p>
            `;
            
            const modal = new bootstrap.Modal(document.getElementById('modalDetalleProducto'));
            modal.show();
        }
    }

    function actualizarEstadisticas() {
        const total = productos.length;
        const activos = productos.filter(p => p.estado === 'activo').length;
        const stockBajo = productos.filter(p => p.stock <= 10 && p.stock > 0).length;
        const valorTotal = productos.reduce((sum, p) => sum + (p.precio * p.stock), 0);
        
        document.getElementById('totalProductos').textContent = total;
        document.getElementById('productosActivos').textContent = activos;
        document.getElementById('stockBajo').textContent = stockBajo;
        document.getElementById('valorInventario').textContent = '$' + formatearPrecio(valorTotal);
    }

    function limpiarFiltros() {
        document.getElementById('buscarProducto').value = '';
        document.getElementById('filtroCategoria').value = '';
        document.getElementById('filtroEstado').value = '';
        cargarProductos();
    }

    function formatearPrecio(precio) {
        return new Intl.NumberFormat('es-CO').format(precio);
    }

    function capitalizar(texto) {
        return texto.charAt(0).toUpperCase() + texto.slice(1);
    }

    function getCategoriaEmoji(categoria) {
        const emojis = {
            'panes': '🍞',
            'pasteles': '🎂',
            'galletas': '🍪',
            'bebidas': '☕',
            'postres': '🍰'
        };
        return emojis[categoria] || '📦';
    }

    function mostrarAlerta(mensaje, tipo) {
        // Puedes implementar tu sistema de notificaciones aquí
        alert(mensaje);
    }
</script>
@endpush