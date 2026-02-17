@extends('layouts.app')

@section('title', 'Gestión de Productos - Panadería')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="{{ asset('css/variables.css') }}" rel="stylesheet">
<link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet">

<style>
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
    .stats-card {
        background: linear-gradient(135deg, #a67c52 0%, #8b6745 100%);
        border-radius: 12px;
        color: white;
    }
    .filter-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .main-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
            <div class="content-wrapper">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-1"><i class="bi bi-box-seam"></i> Gestión de Productos</h2>
                        <p class="text-muted mb-0">Administra el catálogo de productos de tu panadería</p>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProducto" onclick="limpiarFormulario()">
                        <i class="bi bi-plus-circle"></i> Nuevo Producto
                    </button>
                </div>

                <!-- Mensajes de Alerta -->
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

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Estadísticas rápidas -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card stats-card border-0 shadow-sm">
                            <div class="card-body text-center py-3">
                                <i class="bi bi-box-seam" style="font-size: 2.5rem;"></i>
                                <h2 class="mt-2 mb-0" id="totalProductos">{{ isset($productos) ? count($productos) : 0 }}</h2>
                                <p class="mb-0 small">Total Productos</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card stats-card border-0 shadow-sm">
                            <div class="card-body text-center py-3">
                                <i class="bi bi-check-circle" style="font-size: 2.5rem;"></i>
                                <h2 class="mt-2 mb-0" id="productosActivos">{{ isset($productos) ? count($productos) : 0 }}</h2>
                                <p class="mb-0 small">Productos Activos</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card stats-card border-0 shadow-sm">
                            <div class="card-body text-center py-3">
                                <i class="bi bi-exclamation-triangle" style="font-size: 2.5rem;"></i>
                                <h2 class="mt-2 mb-0" id="stockBajo">
                                    {{ isset($productos) ? collect($productos)->filter(function($p){ return isset($p['Stock Minímo:']) && (int)$p['Stock Minímo:'] <= 10 && (int)$p['Stock Minímo:'] > 0; })->count() : 0 }}
                                </h2>
                                <p class="mb-0 small">Stock Bajo</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card stats-card border-0 shadow-sm">
                            <div class="card-body text-center py-3">
                                <i class="bi bi-cash-coin" style="font-size: 2.5rem;"></i>
                                <h2 class="mt-2 mb-0" id="valorInventario">
                                    ${{ isset($productos) ? number_format( collect($productos)->sum(function($p){ return (float)($p['Precio:'] ?? 0) * ((int)($p['Stock Minímo:'] ?? 0)); }), 0, ',', '.') : 0 }}
                                </h2>
                                <p class="mb-0 small">Valor Inventario</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card filter-card mb-4">
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
                                    @foreach ($categorias as $id => $nombre)
                                        <option value="{{ $id }}">{{ $nombre }}</option>
                                    @endforeach
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

                <!-- Tabla de Productos -->
                <div class="card main-card">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0"><i class="bi bi-list-ul"></i> Listado de Productos</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">ID</th>
                                        <th>Nombre</th>
                                        <th>Categoría</th>
                                        <th>Descripción</th>
                                        <th>Precio</th>
                                        <th>Stock</th>
                                        <th>Marca</th>
                                        <th>Estado</th>
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

                                                <td><strong>{{ $producto['Nombre Producto:'] ?? '-' }}</strong></td>

                                                <td class="align-middle">
                                                    <span class="badge bg-info text-dark">
                                                        @php
                                                            $categoriaId = $producto['Id Categoria Producto:'] ?? null;
                                                            $categoriaNombre = $categorias[$categoriaId] ?? 'N/A';
                                                        @endphp
                                                        {{ $categoriaNombre }}
                                                    </span>
                                                </td>

                                                <td><small>{{ Str::limit($producto['Descripcion Producto:'] ?? '', 50) }}</small></td>

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

                                                <td>
                                                    @if(isset($producto['ACTIVO']) && $producto['ACTIVO'])
                                                        <span class="badge bg-success">Activo</span>
                                                    @else
                                                        <span class="badge bg-secondary">Inactivo</span>
                                                    @endif
                                                </td>

                                                <td class="text-center table-actions pe-4">
                                                    <button class="btn btn-sm btn-info btn-action me-1" onclick="verDetalles('{{ $producto['Id Producto:'] ?? '' }}')" title="Ver detalles">
                                                        <i class="bi bi-eye"></i>
                                                    </button>

                                                    <button class="btn btn-sm btn-success btn-action me-1" onclick='editarProducto(@json($producto))' title="Editar">
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
            </div>
        </main>
    </div>
</div>

{{-- MODAL CREAR/EDITAR --}}
<div class="modal fade" id="modalProducto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formProducto" method="POST" action="{{ route('productos.store') }}">
                @csrf
                <input type="hidden" name="_method" id="methodField" value="POST">

                <div class="modal-header" style="background: linear-gradient(135deg, #a67c52 0%, #8b6745 100%); color:white;">
                    <h5 class="modal-title" id="modalTitulo"><i class="bi bi-box-seam"></i> Nuevo Producto</h5>
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
                            <label for="categoriaProducto" class="form-label">
                                <i class="bi bi-grid"></i> Categoría *
                            </label>
                            <select id="categoriaProducto" name="categoria" class="form-select" required>
                                <option value="">Seleccione una categoría</option>
                                @foreach ($categorias as $id => $nombre)
                                    <option value="{{ $id }}">{{ $nombre }}</option>
                                @endforeach
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
                            <label for="marcaProducto" class="form-label"><i class="bi bi-tag-fill"></i> Marca</label>
                            <input type="text" class="form-control" id="marcaProducto" name="marca" value="Propio">
                        </div>

                        <div class="col-md-12">
                            <label for="descripcionProducto" class="form-label"><i class="bi bi-file-text"></i> Descripción</label>
                            <textarea class="form-control" id="descripcionProducto" name="descripcion" rows="3" placeholder="Describe el producto..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" style="background:#a67c52; border: none;">
                        <i class="bi bi-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL DETALLES --}}
<div class="modal fade" id="modalDetalleProducto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #a67c52 0%, #8b6745 100%); color:white;">
                <h5 class="modal-title"><i class="bi bi-info-circle"></i> Detalles del Producto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalleProductoContenido">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const productosData = @json($productos ?? []);
    const categoriaNames = @json($categorias ?? []);

    document.addEventListener('DOMContentLoaded', function() {
        const buscarInput = document.getElementById('buscarProducto');
        if (buscarInput) buscarInput.addEventListener('input', filtrarProductos);

        const filtroCategoria = document.getElementById('filtroCategoria');
        if (filtroCategoria) filtroCategoria.addEventListener('change', filtrarProductos);

        const filtroEstado = document.getElementById('filtroEstado');
        if (filtroEstado) filtroEstado.addEventListener('change', filtrarProductos);
    });

    function filtrarProductos() {
        const busqueda = (document.getElementById('buscarProducto').value || '').toLowerCase();
        const categoriaFiltro = document.getElementById('filtroCategoria').value; 
        const estado = document.getElementById('filtroEstado').value;

        const filas = document.querySelectorAll('#tablaProductos tr[data-id]');
        filas.forEach(fila => {
            const nombre = (fila.getAttribute('data-nombre') || '').toLowerCase();
            const categoriaIdProducto = fila.getAttribute('data-categoria') || ''; 
            const estadoProducto = fila.getAttribute('data-estado') || '';

            const cumpleBusqueda = nombre.includes(busqueda);
            const cumpleCategoria = !categoriaFiltro || String(categoriaIdProducto) === categoriaFiltro;
            const cumpleEstado = !estado || estadoProducto === estado;

            fila.style.display = (cumpleBusqueda && cumpleCategoria && cumpleEstado) ? '' : 'none';
        });
    }

    function limpiarFormulario() {
        const form = document.getElementById('formProducto');
        if (form) form.reset();
        
        document.getElementById('methodField').value = 'POST';
        document.getElementById('formProducto').action = '{{ route("productos.store") }}';
        document.getElementById('modalTitulo').innerHTML = '<i class="bi bi-box-seam"></i> Nuevo Producto';
        document.getElementById('marcaProducto').value = 'Propio';
        document.getElementById('estadoProducto').value = 'activo';
    }

    function editarProducto(producto) {
        console.log('=== EDITANDO PRODUCTO ===');
        console.log('Producto recibido:', producto);
        
        const id = producto['Id Producto:'] ?? producto.ID_PRODUCTO ?? '';
        const nombre = producto['Nombre Producto:'] ?? producto.NOMBRE_PRODUCTO ?? '';
        const precio = producto['Precio:'] ?? producto.PRECIO_PRODUCTO ?? 0;
        const stock = producto['Stock Minímo:'] ?? producto.PRODUCTO_STOCK_MIN ?? 0;
        const descripcion = producto['Descripcion Producto:'] ?? producto.DESCRIPCION_PRODUCTO ?? '';
        const marca = producto['Marca Producto:'] ?? producto.TIPO_PRODUCTO_MARCA ?? 'Propio';
        const categoria = producto['Id Categoria Producto:'] ?? producto.ID_CATEGORIA_PRODUCTO ?? '';
        
        let activo = false;
        if (producto.ACTIVO !== undefined) {
            activo = producto.ACTIVO === true || producto.ACTIVO === 1 || producto.ACTIVO === '1';
        } else if (producto['ACTIVO'] !== undefined) {
            activo = producto['ACTIVO'] === true || producto['ACTIVO'] === 1 || producto['ACTIVO'] === '1';
        }
        
        console.log('Datos extraídos:', {
            id, nombre, precio, stock, descripcion, marca, activo, categoria
        });
        console.log('Estado determinado:', activo ? 'activo' : 'inactivo');
        
        document.getElementById('modalTitulo').innerHTML = '<i class="bi bi-pencil"></i> Editar Producto';
        document.getElementById('methodField').value = 'PATCH';
        document.getElementById('formProducto').action = '/productos/' + id;

        document.getElementById('nombreProducto').value = nombre;
        document.getElementById('precioProducto').value = precio;
        document.getElementById('stockProducto').value = stock;
        document.getElementById('descripcionProducto').value = descripcion || '';
        document.getElementById('marcaProducto').value = marca || 'Propio';
        document.getElementById('categoriaProducto').value = categoria;
        
        const estadoSelect = document.getElementById('estadoProducto');
        estadoSelect.value = activo ? 'activo' : 'inactivo';
        
        console.log('Estado SELECT después de asignar:', estadoSelect.value);

        new bootstrap.Modal(document.getElementById('modalProducto')).show();
    }

    function eliminarProducto(id, nombre) {
        if (!confirm(`¿Está seguro de eliminar el producto "${nombre}"?\n\nEsta acción no se puede deshacer.`)) {
            return;
        }

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
        if (!producto) {
            alert('Producto no encontrado');
            return;
        }

        const categoriaId = producto['Id Categoria Producto:'] ?? '';
        const categoriaNombre = categoriaNames[categoriaId] ?? 'Sin categoría';

        const cont = document.getElementById('detalleProductoContenido');
        cont.innerHTML = `
            <div class="text-center mb-3">
                <img src="${producto['Imagen Producto:'] ?? 'https://via.placeholder.com/250'}" 
                     alt="${producto['Nombre Producto:'] ?? ''}" 
                     class="img-fluid rounded" 
                     style="max-height:250px;"
                     onerror="this.src='https://via.placeholder.com/250'">
            </div>
            <h4 class="text-center mb-3">${producto['Nombre Producto:'] ?? 'Sin nombre'}</h4>
            <hr>
            <div class="row">
                <div class="col-6">
                    <p><strong><i class="bi bi-hash"></i> ID:</strong></p>
                </div>
                <div class="col-6">
                    <p>#${producto['Id Producto:'] ?? '-'}</p>
                </div>
                
                <div class="col-6">
                    <p><strong><i class="bi bi-grid"></i> Categoría:</strong></p>
                </div>
                <div class="col-6">
                    <p><span class="badge bg-info text-dark">${categoriaNombre}</span></p>
                </div>
                
                <div class="col-6">
                    <p><strong><i class="bi bi-currency-dollar"></i> Precio:</strong></p>
                </div>
                <div class="col-6">
                    <p class="text-success fw-bold">$${new Intl.NumberFormat('es-CO').format(parseFloat(producto['Precio:'] || 0))} COP</p>
                </div>
                
                <div class="col-6">
                    <p><strong><i class="bi bi-box"></i> Stock:</strong></p>
                </div>
                <div class="col-6">
                    <p>${producto['Stock Minímo:'] ?? '0'} unidades</p>
                </div>
                
                <div class="col-6">
                    <p><strong><i class="bi bi-tag"></i> Marca:</strong></p>
                </div>
                <div class="col-6">
                    <p>${producto['Marca Producto:'] ?? 'Sin marca'}</p>
                </div>
                
                <div class="col-6">
                    <p><strong><i class="bi bi-calendar"></i> Vencimiento:</strong></p>
                </div>
                <div class="col-6">
                    <p>${producto['Fecha Vencimiento:'] ?? 'N/A'}</p>
                </div>
                
                <div class="col-12">
                    <p><strong><i class="bi bi-file-text"></i> Descripción:</strong></p>
                    <p class="text-muted">${producto['Descripcion Producto:'] ?? 'Sin descripción'}</p>
                </div>
            </div>
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
</script>
@endpush