@extends('layouts.app')

@section('title', 'Gestión de Productos - Panadería')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
{{-- Reemplaza con tu ruta correcta si es necesario --}}
{{-- <link href="{{ asset('css/variables.css') }}" rel="stylesheet"> --}}
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
    
    /* Animación simple para el spinner */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .spin-animation {
        animation: spin 1s linear infinite;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
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
                        {{-- NOTA: Estos son valores fijos. Para un sistema real, deberían cargarse desde la API --}}
                        <option value="Panes">🍞 Panes</option>
                        <option value="Pasteles">🎂 Pasteles</option>
                        <option value="Galletas">🍪 Galletas</option>
                        <option value="Bebidas">☕ Bebidas</option>
                        <option value="Postres">🍰 Postres</option>
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
                        <tr>
                            <td colspan="9" class="text-center py-5 text-primary">
                                <i class="bi bi-arrow-repeat fs-3 spin-animation"></i>
                                <p class="mt-2">Cargando productos...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('dashboard.admin') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver al Dashboard
        </a>
    </div>
</div>

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
                                {{-- Usar los mismos valores que en el filtro --}}
                                <option value="Panes">🍞 Panes</option>
                                <option value="Pasteles">🎂 Pasteles</option>
                                <option value="Galletas">🍪 Galletas</option>
                                <option value="Bebidas">☕ Bebidas</option>
                                <option value="Postres">🍰 Postres</option>
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

<div class="modal fade" id="modalDetalleProducto" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-info-circle"></i> Detalles del Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalleProductoContenido">
                </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let productos = []; 
    const API_URL = 'http://localhost:8080/productos'; 

    document.addEventListener('DOMContentLoaded', function() {
        fetchAndRenderProductos();

        document.getElementById('buscarProducto').addEventListener('input', filtrarProductos);
        document.getElementById('filtroCategoria').addEventListener('change', filtrarProductos);
        document.getElementById('filtroEstado').addEventListener('change', filtrarProductos);

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
    
    // =========================================================
    // 1. CARGA ASÍNCRONA DE DATOS DESDE LA API (CORRECCIÓN DE URL Y CLAVES)
    // =========================================================
    async function fetchAndRenderProductos() {
        try {
            const response = await fetch(API_URL); 
            
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}. URL: ${API_URL}`);
            }

            productos = await response.json(); 
            
            console.log("Productos cargados desde Spring Boot:", productos);
            
            renderTablaProductos(productos);
            actualizarEstadisticas();

        } catch (error) {
            console.error("Error al cargar los productos desde la API:", error);
            const tbody = document.getElementById('tablaProductos');
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-5 text-danger">
                        <i class="bi bi-x-octagon fs-1"></i>
                        <p class="mt-2">Error al conectar con la API de productos.</p>
                        <p>Asegúrese de que el backend esté corriendo en 8080 y, si el error es 'Failed to fetch', active la extensión CORS o configure el proxy.</p>
                        <p>Detalle: ${error.message}</p>
                    </td>
                </tr>
            `;
            document.getElementById('totalProductos').textContent = 0;
            document.getElementById('productosActivos').textContent = 0;
            document.getElementById('stockBajo').textContent = 0;
            document.getElementById('valorInventario').textContent = '$0';
        }
    }

    // =========================================================
    // 2. FUNCIONES PRINCIPALES
    // =========================================================

    function renderTablaProductos(listaProductos) {
        const tbody = document.getElementById('tablaProductos');
        tbody.innerHTML = '';
        
        if (!listaProductos || listaProductos.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <p class="text-muted mt-2">No hay productos registrados o la búsqueda no arrojó resultados.</p>
                    </td>
                </tr>
            `;
            return;
        }

        listaProductos.forEach(producto => {
            const id = producto['Id Producto'];
            const nombre = producto['Nombre Producto'] || 'N/A';
            const descripcionCorta = (producto['Descripcion Producto'] || '').length > 50 
                ? (producto['Descripcion Producto'] || '').substring(0, 50) + '...' 
                : (producto['Descripcion Producto'] || '');
            
            const categoria = producto['Marca Producto'] || 'Sin marca'; 
            
            const estado = producto.activo ? 'activo' : 'inactivo';
            const precio = parseFloat(producto['Precio']) || 0;
            const stock = parseInt(producto['Stock Minimo']) || 0; 
            const imagen = producto['Imagen Url'] || 'https://via.placeholder.com/60';
            
            const tr = document.createElement('tr');
            
            tr.innerHTML = `
                <td class="ps-4"><strong>#${id}</strong></td>
                <td>
                    <img src="${imagen}" alt="${nombre}" class="producto-img" 
                        onerror="this.src='https://via.placeholder.com/60'">
                </td>
                <td><strong>${nombre}</strong></td>
                <td>
                    <span class="badge bg-info text-dark">
                        ${categoria}
                    </span>
                </td>
                <td><small>${descripcionCorta}</small></td>
                <td><strong>$${formatearPrecio(precio)}</strong></td>
                <td>
                    ${stock <= 10 && stock > 0 
                        ? `<span class="badge bg-warning text-dark">${stock}</span>` 
                        : stock === 0 
                        ? `<span class="badge bg-danger">${stock}</span>`
                        : `<span class="badge bg-success">${stock}</span>`
                    }
                </td>
                <td>
                    <span class="badge ${estado === 'activo' ? 'bg-success' : 'bg-danger'}">
                        ${estado === 'activo' ? 'Activo' : 'Inactivo'}
                    </span>
                </td>
                <td class="text-center table-actions pe-4">
                    <button class="btn btn-sm btn-info btn-action me-1" onclick="verDetalles(${id})" title="Ver detalles">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning btn-action me-1" onclick="editarProducto(${id})" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-danger btn-action" onclick="eliminarProducto(${id})" title="Eliminar">
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
            const nombre = (producto['Nombre Producto'] || '').toLowerCase();
            const descripcion = (producto['Descripcion Producto'] || '').toLowerCase();
            const productoCategoria = (producto['Marca Producto'] || '').toLowerCase();
            const productoEstado = producto.activo ? 'activo' : 'inactivo';
            
            const cumpleBusqueda = (nombre.includes(busqueda) || descripcion.includes(busqueda));
            
            const cumpleCategoria = !categoria || productoCategoria.includes(categoria.toLowerCase());
            const cumpleEstado = !estado || productoEstado === estado;
            
            return cumpleBusqueda && cumpleCategoria && cumpleEstado;
        });
        
        renderTablaProductos(productosFiltrados); 
    }

    function actualizarEstadisticas() {
        const total = productos.length;
        const activos = productos.filter(p => p.activo).length;
        
        const stockBajo = productos.filter(p => {
            const stock = parseInt(p['Stock Minimo']) || 0; 
            return stock <= 10 && stock > 0;
        }).length;
        
        const valorTotal = productos.reduce((sum, p) => {
            const precio = parseFloat(p['Precio']) || 0;
            const stock = parseInt(p['Stock Minimo']) || 0;
            return sum + (precio * stock);
        }, 0);
        
        document.getElementById('totalProductos').textContent = total;
        document.getElementById('productosActivos').textContent = activos;
        document.getElementById('stockBajo').textContent = stockBajo;
        
        document.getElementById('valorInventario').textContent = '$' + formatearPrecio(Math.round(valorTotal));
    }
    
    // =========================================================
    // 3. FUNCIONES DE MANTENIMIENTO (CRUD JS en frontend - SIMULADAS)
    // =========================================================

    function guardarProducto() {
        alert("Función 'guardarProducto' no implementada. Necesita hacer un FETCH a la API.");
    }
    
    function editarProducto(id) {
        alert(`Función 'editarProducto' para ID ${id} no implementada.`);
    }

    function eliminarProducto(id) {
        alert(`Función 'eliminarProducto' para ID ${id} no implementada.`);
    }

    function verDetalles(id) {
        alert(`Función 'verDetalles' para ID ${id} no implementada.`);
    }

    function limpiarFiltros() {
        document.getElementById('buscarProducto').value = '';
        document.getElementById('filtroCategoria').value = '';
        document.getElementById('filtroEstado').value = '';
        renderTablaProductos(productos); 
    }

    function limpiarFormulario() {
        document.getElementById('formProducto').reset();
        document.getElementById('productoId').value = '';
        document.getElementById('modalProductoLabel').innerHTML = '<i class="bi bi-box-seam"></i> Nuevo Producto';
        document.getElementById('vistaPrevia').style.display = 'none';
    }

    // =========================================================
    // 4. FUNCIONES DE UTILIDAD
    // =========================================================

    function formatearPrecio(precio) {
        if (typeof precio !== 'number') return '0';
        return new Intl.NumberFormat('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(precio);
    }
    
</script>
@endpush