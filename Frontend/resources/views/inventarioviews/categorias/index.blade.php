{{-- resources/views/inventarioviews/categoriasIngredientes/index.blade.php --}}

@extends('layouts.app') 

@section('title', 'Categorías de Ingredientes - El Castillo del Pan')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* Contenedor Principal con Scroll Interno */
        .main-content {
            height: 100vh;
            overflow-y: auto;
            background-color: #fdfbf9; /* Crema suave */
            padding-bottom: 50px;
        }

        /* Estilo de la Card de Categoría */
        .category-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 20px !important;
            border-left: 6px solid #5d4037 !important; /* Café marca */
            background: white;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(93, 64, 55, 0.1) !important;
        }

        .category-icon {
            width: 55px;
            height: 55px;
            background-color: #f8f5f2;
            color: #5d4037;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        /* Buscador Estilizado */
        .search-wrapper {
            position: relative;
            max-width: 450px;
        }
        .search-wrapper i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #8d6e63;
        }
        .search-input {
            padding-left: 50px !important;
            border-radius: 50px !important;
            height: 48px;
            border: 1px solid #d7ccc8 !important;
            background: white !important;
        }

        /* Botón Marca */
        .btn-crear {
            background-color: #5d4037 !important;
            color: white !important;
            border-radius: 12px !important;
            padding: 10px 25px;
            font-weight: 600;
            border: none;
        }

        /* Scrollbar estética */
        .main-content::-webkit-scrollbar { width: 6px; }
        .main-content::-webkit-scrollbar-thumb { background: #d7ccc8; border-radius: 10px; }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        
        @include('components.admin-sidebar') 
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4 main-content">
            
            {{-- ENCABEZADO --}}
            <div class="d-flex justify-content-between align-items-center pt-4 pb-2 mb-4 border-bottom">
                <div>
                    
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary border-0 me-3" style="color: #5d4037; font-size: 1.5rem; transition: transform 0.2s;">
            <i class="fas fa-arrow-left"></i>
        </a>
                <h1 class="dashboard-page-title" style="color: #3e2723;">Categorías de Ingredientes</h1>
                    
                    
                    <p class="text-muted small mb-0">Organiza los insumos de la panadería</p>
                </div>
                <button class="btn btn-panaderia-action -crear shadow-sm" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fas fa-plus-circle me-2"></i> Nueva Categoría
                </button>
            </div>

            {{-- BUSCADOR --}}
            <div class="mb-5">
                <div class="search-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" id="categoriaSearch" class="form-control search-input shadow-sm" placeholder="Buscar categoría...">
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn btn-panaderia-action -close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- GRID DE CATEGORÍAS --}}
            <div class="row g-4" id="categoriasGrid">
                @forelse($categorias as $cat)
                    <div class="col-md-6 col-xl-4 category-item">
                        <div class="card h-100 shadow-sm category-card">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div class="category-icon shadow-sm">
                                        <i class="fas fa-seedling"></i>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 12px;">
                                            <li>
                                                <a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#editModal-{{ $cat['idCategoriaIngrediente'] }}">
                                                    <i class="fas fa-edit me-2 text-warning"></i> Editar nombre
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form method="POST" action="{{ route('categorias-ingredientes.destroy', $cat['idCategoriaIngrediente']) }}">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger py-2" onclick="return confirm('¿Eliminar esta categoría?')">
                                                        <i class="fas fa-trash me-2"></i> Eliminar
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <h5 class="fw-bold mb-1" style="color: #3e2723;">{{ $cat['nombreCategoria'] }}</h5>
                                <div class="d-flex align-items-center mt-3">
                                    <span class="badge rounded-pill bg-light text-brown border px-3 py-2 small">
                                        ID: #{{ $cat['idCategoriaIngrediente'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- MODAL EDITAR (Dentro del bucle) --}}
                    <div class="modal fade" id="editModal-{{ $cat['idCategoriaIngrediente'] }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                <div class="modal-header border-0 p-4" style="background-color: #f8f5f2; border-radius: 20px 20px 0 0;">
                                    <h5 class="modal-title fw-bold" style="color: #3e2723;"><i class="fas fa-edit me-2 text-warning"></i>Editar Categoría</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" action="{{ route('categorias-ingredientes.update', $cat['idCategoriaIngrediente']) }}">
                                    @csrf @method('PUT')
                                    <div class="modal-body p-4">
                                        <label class="form-label fw-bold small text-uppercase text-muted">Nombre de la Categoría</label>
                                        <input type="text" name="nombreCategoria" class="form-control" value="{{ $cat['nombreCategoria'] }}" required style="border-radius: 10px; height: 45px;">
                                    </div>
                                    <div class="modal-footer border-0 p-4">
                                        <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-crear shadow px-4">Actualizar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-tags fa-3x text-muted mb-3 opacity-25"></i>
                        <p class="text-muted">No hay categorías de ingredientes registradas.</p>
                    </div>
                @endforelse
            </div>

            {{-- MODAL CREAR --}}
            <div class="modal fade" id="crearModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                        <div class="modal-header text-white border-0 p-4" style="background-color: #5d4037; border-radius: 20px 20px 0 0;">
                            <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Nueva Categoría</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST" action="{{ route('categorias-ingredientes.store') }}">
                            @csrf
                            <div class="modal-body p-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-uppercase text-muted">Nombre</label>
                                    <input type="text" name="nombreCategoria" class="form-control" placeholder="Ej: Frutas, Harinas, Lácteos..." required style="border-radius: 10px; height: 45px;">
                                </div>
                            </div>
                            <div class="modal-footer border-0 p-4">
                                <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-crear shadow px-4">Guardar Categoría</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Lógica de búsqueda en tiempo real
        const searchInput = document.getElementById('categoriaSearch');
        searchInput.addEventListener('keyup', function() {
            const term = this.value.toLowerCase();
            const items = document.querySelectorAll('.category-item');
            
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(term) ? 'block' : 'none';
            });
        });
    });
</script>
@endpush