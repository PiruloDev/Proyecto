{{-- resources/views/inventarioviews/proveedores/index.blade.php --}}

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Proveedores - El Castillo del Pan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Tus estilos originales --}}
    <link rel="stylesheet" href="/pre-produccion/PHP Modulos/css/stylemoduloinv.css">

    {{-- Bootstrap y estilos --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">
</head>

<body>

    <div class="container-fluid">
        <div class="row g-0">

            {{-- Sidebar ORIGINAL pero ahora traída con Laravel --}}
            @include('partials.sidebar-inventario')

            {{-- CONTENIDO PRINCIPAL --}}
            <div class="col-md-9 col-lg-10 main-content">

                {{-- NAVBAR SUPERIOR --}}
                    @include('partials.topbarinventario')

                <h1 class="mt-3">Gestión de Proveedores</h1>

                {{-- MENSAJES --}}
                @if (session('success'))
                    <div class="alert alert-success my-3">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger my-3">{{ session('error') }}</div>
                @endif

                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fas fa-plus"></i> Agregar Proveedor
                </button>
                    
                <section id="listado" class="mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2>Listado de Proveedores</h2>

                        <a href="{{ route('proveedores.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-sync"></i> Recargar
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th>Dirección</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($proveedores as $prov)
                                    <tr>
                                        <td>{{ $prov['idProveedor'] }}</td>
                                        <td>{{ $prov['nombreProv'] }}</td>
                                        <td>{{ $prov['telefonoProv'] }}</td>
                                        <td>{{ $prov['emailProv'] }}</td>
                                        <td>{{ $prov['direccionProv'] ?? 'N/A' }}</td>
                                        <td>
                                            @if($prov['activoProv'])
                                                <span class="badge bg-success">Activo</span>
                                            @else
                                                <span class="badge bg-danger">Inactivo</span>
                                            @endif
                                        </td>

                                        <td>
                                            {{-- BOTÓN EDITAR (Modal) --}}
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editModal-{{ $prov['idProveedor'] }}">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            {{-- BOTÓN ELIMINAR --}}
                                            <form method="POST"
                                                action="{{ route('proveedores.destroy', $prov['idProveedor']) }}"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('¿Eliminar este proveedor?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    {{-- ================== MODAL EDITAR ================== --}}
                                    <div class="modal fade" id="editModal-{{ $prov['idProveedor'] }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">

                                                <div class="modal-header">
                                                    <h5 class="modal-title">Editar Proveedor</h5>
                                                    <button class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <form method="POST"
                                                    action="{{ route('proveedores.update', $prov['idProveedor']) }}"
                                                    class="row g-3 p-3">
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="col-md-6">
                                                        <label class="form-label">Nombre del Proveedor</label>
                                                        <input type="text" name="nombreProv"
                                                            class="form-control"
                                                            value="{{ $prov['nombreProv'] }}" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Teléfono</label>
                                                        <input type="text" name="telefonoProv"
                                                            class="form-control"
                                                            value="{{ $prov['telefonoProv'] }}" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Email</label>
                                                        <input type="email" name="emailProv" class="form-control"
                                                            value="{{ $prov['emailProv'] }}" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Estado</label>
                                                        <select name="activoProv" class="form-select" required>
                                                            <option value="1" {{ $prov['activoProv'] ? 'selected' : '' }}>Activo</option>
                                                            <option value="0" {{ !$prov['activoProv'] ? 'selected' : '' }}>Inactivo</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-12">
                                                        <label class="form-label">Dirección</label>
                                                        <textarea name="direccionProv" class="form-control" rows="2" required>{{ $prov['direccionProv'] }}</textarea>
                                                    </div>

                                                    <div class="col-12">
                                                        <button type="submit" class="btn btn-warning w-100">
                                                            Guardar Cambios
                                                        </button>
                                                    </div>

                                                </form>

                                            </div>
                                        </div>
                                    </div>

                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No hay proveedores registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- MODAL CREAR PROVEEDOR --}}
                <section class="mb-5">
                    <div class="modal fade" id="crearModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content p-3">

                                <div class="modal-header">
                                    <h5 class="modal-title">Agregar Nuevo Proveedor</h5>
                                    <button class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <form method="POST" action="{{ route('proveedores.store') }}" class="row g-3 p-3">
                                    @csrf

                                    <div class="col-md-6">
                                        <label class="form-label">Nombre del Proveedor</label>
                                        <input type="text" name="nombreProv" class="form-control" 
                                               placeholder="Ej: Distribuidora XYZ" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text" name="telefonoProv" class="form-control"
                                               placeholder="Ej: +57 300 123 4567" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="emailProv" class="form-control"
                                               placeholder="proveedor@ejemplo.com" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Estado</label>
                                        <select name="activoProv" class="form-select" required>
                                            <option value="1" selected>Activo</option>
                                            <option value="0">Inactivo</option>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Dirección (Opcional)</label>
                                        <textarea name="direccionProv" class="form-control" rows="2" 
                                                  placeholder="Dirección completa del proveedor"></textarea>
                                    </div>

                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary w-100">
                                            Guardar
                                        </button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </section>

            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>