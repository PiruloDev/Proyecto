{{-- resources/views/inventarioviews/categoriasIngredientes/index.blade.php --}}

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Categorías de Ingredientes - El Castillo del Pan</title>
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

                {{-- NAVBAR SUPERIOR ORIGINAL --}}
                <nav class="navbar navbar-expand-lg top-navbar">
                    <div class="container-fluid">
                        <a class="navbar-brand d-md-none" href="#">Menú</a>
                        <div class="collapse navbar-collapse justify-content-end">
                            <div class="navbar-nav">
                                <div class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                                        data-bs-toggle="dropdown">
                                        <span class="me-2 text-dark d-none d-sm-inline">Administrador</span>
                                        <div class="profile-icon-wrapper"><i class="fas fa-user"></i></div>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>
                                                Configuración</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i>
                                                Cerrar Sesión</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>

                <h1 class="mt-3">Gestión de Categorías de Ingredientes</h1>

                {{-- MENSAJES --}}
                @if (session('success'))
                    <div class="alert alert-success my-3">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger my-3">{{ session('error') }}</div>
                @endif

                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fas fa-plus"></i> Agregar Categoría
                </button>
                    
                <section id="listado" class="mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2>Listado de Categorías</h2>

                        <a href="{{ route('categorias-ingredientes.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-sync"></i> Recargar
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre Categoría</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($categorias as $cat)
                                    <tr>
                                        <td>{{ $cat['idCategoriaIngrediente'] }}</td>
                                        <td>{{ $cat['nombreCategoria'] }}</td>

                                        <td>
                                            {{-- BOTÓN EDITAR (Modal) --}}
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editModal-{{ $cat['idCategoriaIngrediente'] }}">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            {{-- BOTÓN ELIMINAR --}}
                                            <form method="POST"
                                                action="{{ route('categorias-ingredientes.destroy', $cat['idCategoriaIngrediente']) }}"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('¿Eliminar esta categoría?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    {{-- ================== MODAL EDITAR ================== --}}
                                    <div class="modal fade" id="editModal-{{ $cat['idCategoriaIngrediente'] }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">

                                                <div class="modal-header">
                                                    <h5 class="modal-title">Editar Categoría</h5>
                                                    <button class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <form method="POST"
                                                    action="{{ route('categorias-ingredientes.update', $cat['idCategoriaIngrediente']) }}"
                                                    class="p-3">
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="mb-3">
                                                        <label class="form-label">Nombre de la Categoría</label>
                                                        <input type="text" name="nombreCategoria"
                                                            class="form-control"
                                                            value="{{ $cat['nombreCategoria'] }}" required>
                                                    </div>

                                                    <div class="d-grid">
                                                        <button type="submit" class="btn btn-warning">
                                                            Guardar Cambios
                                                        </button>
                                                    </div>

                                                </form>

                                            </div>
                                        </div>
                                    </div>

                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No hay categorías registradas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- MODAL CREAR CATEGORÍA --}}
                <section class="mb-5">
                    <div class="modal fade" id="crearModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content p-3">

                                <div class="modal-header">
                                    <h5 class="modal-title">Agregar Nueva Categoría</h5>
                                    <button class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <form method="POST" action="{{ route('categorias-ingredientes.store') }}" class="p-3">
                                    @csrf

                                    <div class="mb-3">
                                        <label class="form-label">Nombre de la Categoría</label>
                                        <input type="text" name="nombreCategoria" class="form-control" 
                                               placeholder="Ej: Lácteos, Carnes, Verduras..." required>
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">
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