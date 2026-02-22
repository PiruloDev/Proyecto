{{-- resources/views/inventarioviews/recetas/index.blade.php --}}

@extends('layouts.app') 

@section('title', 'Gestión de Recetas - El Castillo del Pan')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        .main-content {
            height: 100vh;
            overflow-y: auto;
            padding-bottom: 3rem;
            background: linear-gradient(135deg, var(--panaderia-beige-claro) 0%, var(--panaderia-blanco-calido) 100%);
        }

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

        .recipe-icon-wrapper {
            width: 50px; height: 50px;
            background-color: rgba(166, 124, 82, 0.1);
            color: var(--panaderia-marron-principal);
            border-radius: 15px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
        }

        .search-container { position: relative; }
        .search-container i {
            position: absolute; left: 18px; top: 50%;
            transform: translateY(-50%);
            color: var(--panaderia-marron-principal);
        }
        .search-input {
            padding-left: 50px !important;
            border-radius: 50px !important;
            height: 50px;
            border: 1px solid var(--panaderia-beige-oscuro) !important;
        }

        .btn-panaderia {
            background: var(--panaderia-marron-principal);
            color: white; border-radius: 12px; transition: all 0.3s;
        }
        .btn-panaderia:hover { background: var(--panaderia-marron-hover); color: white; }

        .preview-table {
            background: rgba(255, 255, 255, 0.5);
            border-radius: 12px; font-size: 0.85rem;
        }

        .unidad-badge {
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            font-weight: bold;
            color: var(--panaderia-marron-oscuro);
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

            {{-- Alertas de éxito / error --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Buscador --}}
            <div class="row mb-4">
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
                                                    <button type="submit" class="dropdown-item rounded-2 text-danger" onclick="return confirm('¿Eliminar?')"><i class="fas fa-trash me-2"></i>Eliminar</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <h5 class="fw-bold mb-1" style="color: var(--panaderia-marron-oscuro);">{{ $receta['nombreProducto'] }}</h5>
                                <span class="badge rounded-pill bg-white text-muted border mb-3 w-fit">ID Producto: {{ $receta['idProducto'] }}</span>

                                <div class="preview-table p-2 mb-4">
                                    <table class="table table-sm table-borderless mb-0">
                                        <thead>
                                            <tr class="text-muted border-bottom" style="font-size: 0.75rem;">
                                                <th class="ps-2">Ingrediente</th>
                                                <th class="text-end pe-2">Cant.</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- ✅ Protegido contra campos null --}}
                                            @foreach(array_slice($receta['detalles'], 0, 3) as $detalle)
                                                <tr>
                                                    <td class="ps-2 text-secondary">
                                                        <i class="bi bi-dot text-warning"></i>
                                                        {{ isset($detalle['nombreIngrediente']) ? $detalle['nombreIngrediente'] : 'Ingrediente ID '.($detalle['idIngrediente'] ?? '?') }}
                                                    </td>
                                                    <td class="text-end pe-2 fw-bold">
                                                        {{ number_format($detalle['cantidadRequerida'] ?? 0, 2) }}
                                                        <small class="text-muted fw-normal">{{ $detalle['nombreUnidad'] ?? 'N/A' }}</small>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
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
                        <h4 class="text-muted">No hay recetas registradas</h4>
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

            <form method="POST" action="{{ route('recetas.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small text-uppercase">Producto a Producir</label>
                        <select name="idProducto" class="form-select shadow-sm p-3" style="border-radius: 12px;" required>
                            <option value="">Seleccione el producto...</option>
                            @foreach($productos as $prod)
                                <option value="{{ $prod->ID_PRODUCTO }}">{{ $prod->NOMBRE_PRODUCTO }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0"><i class="fas fa-list me-2"></i>Composición</h6>
                        <button type="button" id="add-detail-btn" class="btn btn-sm btn-panaderia px-3 shadow-sm">
                            <i class="fas fa-plus me-1"></i> Agregar Insumo
                        </button>
                    </div>
                    
                    <div id="detalles-container" class="p-3 rounded-4" style="background: rgba(0,0,0,0.02); border: 1px dashed #ccc;">
                        {{-- JS llenará esto --}}
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-panaderia px-5 fw-bold shadow">Registrar Receta</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Buscador
    document.getElementById('searchInput')?.addEventListener('keyup', function() {
        const value = this.value.toLowerCase();
        document.querySelectorAll('.receta-item').forEach(item => {
            item.style.display = item.innerText.toLowerCase().includes(value) ? '' : 'none';
        });
    });

    // Lógica de Ingredientes Dinámicos
    const container = document.getElementById('detalles-container');
    const addBtn = document.getElementById('add-detail-btn');
    let index = 0;

    // ✅ Variable con ingredientes desde el controlador
    const insumosDisponibles = @json($ingredientes ?? []);

    addBtn.addEventListener('click', () => {
        const div = document.createElement('div');
        div.className = 'row g-2 mb-3 align-items-end animate__animated animate__fadeIn';

        // ✅ Se agrega data-id-unidad para poder enviar el ID de la unidad al servidor
        let options = insumosDisponibles.map(i =>
            `<option value="${i.idIngrediente}" data-uni="${i.abreviaturaUnidad}" data-id-unidad="${i.idUnidad}">${i.nombreIngrediente}</option>`
        ).join('');

        div.innerHTML = `
            <div class="col-md-5">
                <label class="small fw-bold">Ingrediente</label>
                <select name="detalles[${index}][idIngrediente]" class="form-select select-insumo" required>
                    <option value="">Seleccionar...</option>
                    ${options}
                </select>
            </div>
            <div class="col-md-4">
                <label class="small fw-bold">Cantidad</label>
                <input type="number" step="0.001" name="detalles[${index}][cantidadRequerida]" class="form-control" placeholder="0.000" required>
            </div>
            <div class="col-md-2">
                <label class="small fw-bold">Unidad</label>
                <div class="unidad-badge">---</div>
                {{-- ✅ Input hidden que guarda el ID de la unidad para enviarlo al servidor --}}
                <input type="hidden" name="detalles[${index}][idUnidad]" class="input-unidad" value="">
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-outline-danger btn-sm border-0 mb-1" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        container.appendChild(div);
        index++;
    });

    // ✅ Evento actualiza tanto el badge visual como el input hidden con el ID de unidad
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('select-insumo')) {
            const option = e.target.options[e.target.selectedIndex];
            const uni = option.getAttribute('data-uni') || '---';
            const idUnidad = option.getAttribute('data-id-unidad') || '';

            const row = e.target.closest('.row');
            const badge = row.querySelector('.unidad-badge');
            const hiddenUnidad = row.querySelector('.input-unidad');

            if (badge) badge.textContent = uni;
            if (hiddenUnidad) hiddenUnidad.value = idUnidad; // ✅ guarda el ID real
        }
    });
</script>
@endpush