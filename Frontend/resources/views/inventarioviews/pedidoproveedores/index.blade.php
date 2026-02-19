{{-- resources/views/inventarioviews/pedidoproveedores/index.blade.php --}}

@extends('layouts.app') 

@section('title', 'Gestión de Pedidos - El Castillo del Pan')

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
            background-color: #fdfbf9; /* Color crema suave */
            padding-bottom: 100px;
        }

        /* Estilo de Tarjetas de Pedido */
        .pedido-card {
            transition: all 0.3s ease;
            border-left: 6px solid #5d4037 !important; /* Acento café marca */
            border-radius: 15px !important;
            background: white;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .pedido-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(93, 64, 55, 0.1) !important;
        }

        /* Buscador estilizado */
        .search-wrapper {
            position: relative;
            max-width: 500px;
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

        /* Botón estilo Panadería */
        .btn-crear {
            background-color: #5d4037 !important;
            color: white !important;
            border-radius: 12px !important;
            padding: 10px 25px;
            font-weight: 600;
            border: none;
        }

        .btn-crear:hover {
            background-color: #3e2723 !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* Scrollbar estética */
        .main-content::-webkit-scrollbar { width: 8px; }
        .main-content::-webkit-scrollbar-thumb { background: #d7ccc8; border-radius: 10px; }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        {{-- Side Bar --}}
        @include('components.admin-sidebar') 

        <main class="col-md-9 ms-sm-auto col-lg-10 px-4 main-content">
            
            {{-- HEADER Y BUSCADOR --}}
            <div class="d-flex justify-content-between align-items-center flex-wrap pt-4 pb-2 mb-4 border-bottom">
                <div>
                    <h1 class="h2 fw-bold" style="color: #3e2723;">Pedidos a Proveedores</h1>
                    <p class="text-muted">Gestión de suministros e insumos para la panadería.</p>
                </div>
                <button class="btn btn-crear shadow-sm" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fas fa-plus-circle me-2"></i> Nuevo Pedido
                </button>
            </div>

            {{-- ZONA DE BUSQUEDA --}}
            <div class="mb-5">
                <div class="search-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" id="pedidoSearch" class="form-control search-input shadow-sm" placeholder="Buscar por número de pedido o ID de proveedor...">
                </div>
            </div>

            {{-- MENSAJES DE ALERTA --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- GRID DE PEDIDOS --}}
            <div class="row g-4" id="pedidosGrid">
                @forelse($pedidos as $pedido)
                    <div class="col-md-6 col-xl-4 pedido-item">
                        <div class="card pedido-card shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="badge bg-light text-brown border px-2 py-1 small">#{{ $pedido['numeroPedido'] }}</span>
                                    @php
                                        $badgeClass = match ($pedido['estadoPedido']) {
                                            'PENDIENTE' => 'bg-warning text-dark',
                                            'COMPLETADO' => 'bg-success text-white',
                                            'CANCELADO' => 'bg-danger text-white',
                                            default => 'bg-secondary text-white',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} rounded-pill px-3">{{ $pedido['estadoPedido'] }}</span>
                                </div>

                                <h5 class="fw-bold mb-1" style="color: #5d4037;">Proveedor ID: {{ $pedido['idProveedor'] }}</h5>
                                <p class="text-muted small mb-4">
                                    <i class="far fa-calendar-alt me-1"></i> 
                                    Registrado el: {{ \Carbon\Carbon::parse($pedido['fechaPedido'])->format('d/m/Y') }}
                                </p>

                                <div class="d-flex gap-2 pt-3 border-top">
                                    <a href="{{ route('pedidoproveedores.show', $pedido['idPedidoProv']) }}" 
                                       class="btn btn-sm btn-outline-secondary flex-grow-1">
                                        <i class="fas fa-eye me-1"></i> Detalles
                                    </a>
                                    
                                    <form method="POST" action="{{ route('pedidoproveedores.destroy', $pedido['idPedidoProv']) }}" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('¿Desea eliminar permanentemente este pedido?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-receipt fa-3x text-muted mb-3 opacity-25"></i>
                        <p class="text-muted">No se encontraron pedidos en la base de datos.</p>
                    </div>
                @endforelse
            </div>

            {{-- MODAL CREAR PEDIDO --}}
            <div class="modal fade" id="crearModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                        <div class="modal-header text-white" style="background-color: #5d4037; border-radius: 20px 20px 0 0;">
                            <h5 class="modal-title fw-bold"><i class="fas fa-cart-plus me-2"></i> Registrar Nuevo Pedido</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <form method="POST" action="{{ route('pedidoproveedores.store') }}">
                            @csrf
                            <div class="modal-body p-4">
                                {{-- Cabecera del Formulario --}}
                                <div class="row g-3 mb-4 p-4 rounded-3" style="background-color: #fdfbf9; border: 1px dashed #d7ccc8;">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">ID Proveedor</label>
                                        <input type="number" name="idProveedor" class="form-control" required placeholder="Ej: 1">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Número de Pedido (Referencia)</label>
                                        <input type="number" name="numeroPedido" class="form-control" required placeholder="Ej: 9999">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Estado Inicial</label>
                                        <select name="estadoPedido" class="form-select" required>
                                            <option value="PENDIENTE" selected>PENDIENTE</option>
                                            <option value="COMPLETADO">COMPLETADO</option>
                                            <option value="CANCELADO">CANCELADO</option>
                                        </select>
                                    </div>
                                </div>
                                
                                {{-- Sección de Detalles Dinámicos --}}
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-bold mb-0" style="color: #8d6e63;"><i class="fas fa-boxes me-2"></i>Ingredientes a Solicitar</h5>
                                    <button type="button" id="add-detail-btn" class="btn btn-sm btn-success rounded-pill">
                                        <i class="fas fa-plus me-1"></i> Agregar Ingrediente
                                    </button>
                                </div>

                                <div id="detalles-container">
                                    {{-- Fila base --}}
                                    <div class="row g-2 detalle-row mb-2 p-3 bg-light rounded-3 align-items-end border">
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-muted">ID Ingrediente</label>
                                            <input type="number" name="detalles[0][idIngrediente]" class="form-control" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-muted">Cantidad</label>
                                            <input type="number" name="detalles[0][cantidad]" class="form-control" required min="1">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-muted">Precio Unitario ($)</label>
                                            <input type="number" step="0.01" name="detalles[0][precioUnitario]" class="form-control" required min="0">
                                        </div>
                                        <div class="col-md-2 text-end">
                                            <button type="button" class="btn btn-outline-danger w-100 remove-detail-btn" disabled>
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer border-0 bg-light p-3" style="border-radius: 0 0 20px 20px;">
                                <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-crear shadow">
                                    <i class="fas fa-save me-2"></i> Procesar Pedido
                                </button>
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
        
        // 1. FUNCIONALIDAD DEL BUSCADOR
        const searchInput = document.getElementById('pedidoSearch');
        searchInput.addEventListener('keyup', function() {
            const term = this.value.toLowerCase();
            const items = document.querySelectorAll('.pedido-item');
            
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(term) ? 'block' : 'none';
            });
        });

        // 2. AGREGAR FILAS DINÁMICAS AL MODAL
        let detailIndex = 1;
        const container = document.getElementById('detalles-container');
        const addButton = document.getElementById('add-detail-btn');

        addButton.addEventListener('click', function() {
            const newRow = document.createElement('div');
            newRow.className = 'row g-2 detalle-row mb-2 p-3 bg-light rounded-3 align-items-end border animate__animated animate__fadeInUp';
            newRow.innerHTML = `
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">ID Ingrediente</label>
                    <input type="number" name="detalles[${detailIndex}][idIngrediente]" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Cantidad</label>
                    <input type="number" name="detalles[${detailIndex}][cantidad]" class="form-control" required min="1">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Precio Unitario ($)</label>
                    <input type="number" step="0.01" name="detalles[${detailIndex}][precioUnitario]" class="form-control" required min="0">
                </div>
                <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-outline-danger w-100 remove-detail-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            container.appendChild(newRow);
            detailIndex++;
            updateRemoveButtonsState();
        });

        // 3. ELIMINAR FILAS
        container.addEventListener('click', function(e) {
            if (e.target.closest('.remove-detail-btn')) {
                e.target.closest('.detalle-row').remove();
                updateRemoveButtonsState();
            }
        });

        function updateRemoveButtonsState() {
            const rows = container.querySelectorAll('.detalle-row');
            rows.forEach(row => {
                const btn = row.querySelector('.remove-detail-btn');
                btn.disabled = (rows.length === 1);
            });
        }
    });
</script>
@endpush