{{-- resources/views/inventarioviews/recetas/index.blade.php --}}

@extends('layouts.app') 

@section('title', 'Gestión de Recetas - El Castillo del Pan')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* Contenedor principal con scroll interno */
        .main-content {
            height: 100vh;
            overflow-y: auto;
            padding-bottom: 3rem;
            background: linear-gradient(135deg, var(--panaderia-beige-claro) 0%, var(--panaderia-blanco-calido) 100%);
        }

        /* Card Estilo Glass para Recetas */
        .receta-card {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            border: 1px solid rgba(187, 148, 103, 0.2) !important;
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
            border-radius: 20px !important;
            border-top: 6px solid var(--panaderia-marron-principal) !important;
        }

        .receta-card:hover {
            transform: translateY(-8px);
            background: rgba(255, 255, 255, 0.9);
            box-shadow: var(--panaderia-shadow-xl) !important;
        }

        /* Icono de receta circular */
        .recipe-icon-wrapper {
            width: 50px;
            height: 50px;
            background-color: rgba(166, 124, 82, 0.1);
            color: var(--panaderia-marron-principal);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        /* Buscador estilizado */
        .search-container { position: relative; }
        .search-container i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--panaderia-marron-principal);
        }
        .search-input {
            padding-left: 50px !important;
            border-radius: 50px !important;
            height: 50px;
            border: 1px solid var(--panaderia-beige-oscuro) !important;
            box-shadow: var(--panaderia-shadow-sm);
        }

        /* Botones personalizados */
        .btn-panaderia {
            background: var(--panaderia-marron-principal);
            color: white;
            border-radius: 12px;
            transition: all 0.3s;
        }
        .btn-panaderia:hover {
            background: var(--panaderia-marron-hover);
            color: white;
            transform: scale(1.02);
        }

        /* Mini tabla interna de ingredientes */
        .preview-table {
            background: rgba(255, 255, 255, 0.5);
            border-radius: 12px;
            font-size: 0.85rem;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('components.admin-sidebar') 
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4 main-content">
            
            {{-- Header --}}
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-2 mb-4">
                <div>
                    <h1 class="h2 fw-bold" style="color: var(--panaderia-marron-oscuro);">Fichas Técnicas (Recetas)</h1>
                    <p class="text-muted small">Gestión de proporciones e ingredientes por producto.</p>
                </div>
                <button class="btn btn-panaderia px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fas fa-plus-circle me-2"></i> Nueva Receta
                </button>
            </div>

            {{-- Buscador --}}
            <div class="row mb-5">
                <div class="col-md-6 col-lg-5">
                    <div class="search-container">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" class="form-control search-input" placeholder="Buscar por producto o ID...">
                    </div>
                </div>
            </div>

            {{-- Grid de Recetas --}}
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4" id="recetasGrid">
                @forelse($recetas as $receta)
                    <div class="col receta-item">
                        <div class="card h-100 receta-card border-0 shadow-sm">
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="recipe-icon-wrapper">
                                        <i class="fas fa-utensils"></i>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2" style="border-radius: 15px;">
                                            <li><a class="dropdown-item rounded-2" href="{{ route('recetas.show', $receta['idProducto']) }}"><i class="fas fa-eye me-2 text-primary"></i>Ver detalle</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('recetas.destroy', $receta['idProducto']) }}" method="POST">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item rounded-2 text-danger" onclick="return confirm('¿Eliminar esta receta?')"><i class="fas fa-trash me-2"></i>Eliminar</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <h5 class="fw-bold mb-1" style="color: var(--panaderia-marron-oscuro);">{{ $receta['nombreProducto'] }}</h5>
                                <span class="badge rounded-pill bg-white text-muted border mb-3">ID: {{ $receta['idProducto'] }}</span>

                                {{-- Vista Previa de Ingredientes --}}
                                <div class="preview-table p-2 mb-4">
                                    <table class="table table-sm table-borderless mb-0">
                                        <thead>
                                            <tr class="text-muted border-bottom" style="font-size: 0.75rem;">
                                                <th class="ps-2">ID Insumo</th>
                                                <th class="text-end pe-2">Cant.</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(array_slice($receta['detalles'], 0, 3) as $detalle)
                                                <tr>
                                                    <td class="ps-2 text-secondary"><i class="bi bi-dot"></i> {{ $detalle['idIngrediente'] }}</td>
                                                    <td class="text-end pe-2 fw-bold">{{ number_format($detalle['cantidadRequerida'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if(count($receta['detalles']) > 3)
                                        <div class="text-center pt-2 border-top mt-1">
                                            <small class="text-muted italic">+ {{ count($receta['detalles']) - 3 }} ingredientes adicionales</small>
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-auto">
                                    <a href="{{ route('recetas.show', $receta['idProducto']) }}" class="btn btn-outline-dark btn-sm w-100 rounded-pill border-2 fw-bold">
                                        Administrar Ficha
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="glass-card p-5 rounded-4 shadow-sm">
                            <i class="fas fa-book-open fa-3x text-muted opacity-25 mb-3"></i>
                            <h4 class="text-muted">No hay recetas registradas</h4>
                        </div>
                    </div>
                @endforelse
            </div>
        </main>
    </div>
</div>

{{-- MODAL CREAR --}}
<div class="modal fade" id="crearModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0" style="border-radius: 25px;">
            <div class="modal-header text-white border-0" style="background: var(--panaderia-marron-oscuro); border-radius: 25px 25px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fas fa-folder-plus me-2 text-warning"></i>Nueva Ficha Técnica</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('recetas.store') }}" id="recetaForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small text-uppercase">Producto a Producir</label>
                        <select name="idProducto" class="form-select shadow-sm p-3" style="border-radius: 12px; border: 1px solid var(--panaderia-beige-oscuro);" required>
                            <option value="">Seleccione el producto del catálogo...</option>
                            @foreach($productos as $prod)
                                <option value="{{ $prod->ID_PRODUCTO }}">{{ $prod->NOMBRE_PRODUCTO }} (ID: {{ $prod->ID_PRODUCTO }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0" style="color: var(--panaderia-marron-principal);"><i class="fas fa-list me-2"></i>Composición de Ingredientes</h6>
                        <button type="button" id="add-detail-btn" class="btn btn-sm btn-panaderia px-3 shadow-sm">
                            <i class="fas fa-plus me-1"></i> Agregar Insumo
                        </button>
                    </div>
                    
                    <div id="detalles-container" class="p-3 rounded-4" style="background: rgba(0,0,0,0.03); border: 1px dashed var(--panaderia-beige-oscuro);">
                        {{-- Se llena con JS --}}
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light px-4 rounded-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-panaderia px-5 fw-bold shadow">Registrar Receta</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Buscador funcional para filtrar las cards de recetas
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const value = this.value.toLowerCase();
            const items = document.querySelectorAll('.receta-item');
            
            items.forEach(item => {
                const text = item.innerText.toLowerCase();
                item.style.display = text.includes(value) ? '' : 'none';
            });
        });
    }

    // Lógica dinámica para agregar filas de detalles (Input manual de ID)
    const container = document.getElementById('detalles-container');
    const addBtn = document.getElementById('add-detail-btn');
    let index = 0;

    if (addBtn) {
        addBtn.addEventListener('click', () => {
            const div = document.createElement('div');
            div.className = 'row g-2 mb-2 align-items-center animate__animated animate__fadeInUp';
            div.innerHTML = `
                <div class="col-md-7">
                    <input type="number" name="detalles[${index}][idIngrediente]" 
                           class="form-control shadow-sm" 
                           placeholder="ID del Insumo" 
                           style="border-radius: 10px;" required>
                </div>
                <div class="col-md-4">
                    <input type="number" step="0.001" name="detalles[${index}][cantidadRequerida]" 
                           class="form-control shadow-sm" 
                           placeholder="Cant." 
                           style="border-radius: 10px;" required>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger btn-sm border-0" 
                            onclick="this.parentElement.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            container.appendChild(div);
            index++;
        });
    }
</script>
@endpush