@extends('layouts.app') 

@section('title', 'Gestión de Pedidos - El Castillo del Pan')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        .main-content {
            height: 100vh;
            overflow-y: auto;
            background-color: #fdfbf9;
            padding-bottom: 100px;
        }

        .pedido-card {
            transition: all 0.3s ease;
            border-left: 6px solid #5d4037 !important;
            border-radius: 15px !important;
            background: white;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .pedido-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(93, 64, 55, 0.1) !important;
        }

        .search-wrapper { position: relative; max-width: 500px; }
        .search-wrapper i {
            position: absolute; left: 18px; top: 50%;
            transform: translateY(-50%); color: #8d6e63;
        }
        .search-input {
            padding-left: 50px !important;
            border-radius: 50px !important;
            height: 48px;
            border: 1px solid #d7ccc8 !important;
            background: white !important;
        }

        .btn-crear {
            background-color: #5d4037 !important;
            color: white !important;
            border-radius: 12px !important;
            padding: 10px 25px;
            font-weight: 600;
            border: none;
        }
        .btn-crear:hover { background-color: #3e2723 !important; }

        .tabla-ingredientes th {
            background-color: #efebe9;
            color: #4e342e;
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        .total-cop {
            background: #efebe9;
            border-radius: 10px;
            padding: 8px 15px;
            font-weight: 700;
            color: #3e2723;
        }

        .badge-entregado { background-color: #2e7d32; }
        .badge-pendiente { background-color: #f57f17; color: #000; }
        .badge-cancelado { background-color: #b71c1c; }
        .badge-completado { background-color: #1565c0; }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('components.admin-sidebar') 

        <main class="col-md-9 ms-sm-auto col-lg-10 px-4 main-content">
            
            {{-- HEADER --}}
<div class="d-flex justify-content-between align-items-center flex-wrap pt-4 pb-2 mb-4 border-bottom">
    <div class="d-flex align-items-center">
        <a href="{{ route('dashboard.inventario') }}" class="btn btn-outline-secondary border-0 me-3" style="color: #5d4037; font-size: 1.5rem; transition: transform 0.2s;">
            <i class="fas fa-arrow-left"></i>
        </a>
        
        <div>
            <h1 class="h2 fw-bold mb-0" style="color: #3e2723;">Pedidos a Proveedores</h1>
            <p class="text-muted mb-0">Gestión de suministros e insumos para la panadería.</p>
        </div>
    </div>
    
    <button class="btn btn-crear shadow-sm" data-bs-toggle="modal" data-bs-target="#crearModal">
        <i class="fas fa-plus-circle me-2"></i> Nuevo Pedido
    </button>
</div>

            {{-- BUSCADOR --}}
            <div class="mb-5">
                <div class="search-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" id="pedidoSearch" class="form-control search-input shadow-sm"
                           placeholder="Buscar por número de pedido o proveedor...">
                </div>
            </div>

            {{-- ALERTAS --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4"
                     style="border-radius: 15px;">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4"
                     style="border-radius: 15px;">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- GRID DE PEDIDOS --}}
            <div class="row g-4" id="pedidosGrid">
                @forelse($pedidos as $pedido)
                @php
                    $estado = $pedido['estadoPedido'] ?? 'PENDIENTE';
                    $badgeClass = match(strtoupper($estado)) {
                        'ENTREGADO'  => 'badge-entregado text-white',
                        'COMPLETADO' => 'badge-completado text-white',
                        'CANCELADO'  => 'badge-cancelado text-white',
                        default      => 'badge-pendiente',
                    };
                    $totalCOP = 0;
                    if (!empty($pedido['detalles'])) {
                        foreach ($pedido['detalles'] as $det) {
                            $totalCOP += ($det['precioUnitario'] ?? 0) * ($det['cantidad'] ?? 0);
                        }
                    }
                    // ← Resolver nombre del proveedor
                    $nombreProv = collect($proveedores)
                        ->firstWhere('idProveedor', $pedido['idProveedor'])['nombreProv'] 
                        ?? 'Proveedor ID: ' . $pedido['idProveedor'];
                @endphp
                <div class="col-md-6 col-xl-4 pedido-item">
                    <div class="card pedido-card shadow-sm h-100">
                        <div class="card-body p-4 d-flex flex-column">

                            {{-- Cabecera de la card --}}
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-light text-dark border px-2 py-1 small fw-bold">
                                    #{{ $pedido['numeroPedido'] }}
                                </span>
                                <span class="badge {{ $badgeClass }} rounded-pill px-3">
                                    {{ strtoupper($estado) }}
                                </span>
                            </div>

                            <h5 class="fw-bold mb-1" style="color: #5d4037;">
                                <i class="bi bi-truck me-2"></i>{{ $nombreProv }}
                            </h5>
                            <p class="text-muted small mb-3">
                                <i class="far fa-calendar-alt me-1"></i>
                                {{ \Carbon\Carbon::parse($pedido['fechaPedido'])->format('d/m/Y') }}
                            </p>

                            {{-- Tabla de ingredientes si el pedido trae detalles --}}
                            @if(!empty($pedido['detalles']))
                                <div class="mb-3">
                                    <table class="table table-sm tabla-ingredientes rounded-3 overflow-hidden mb-2">
                                        <thead>
                                            <tr>
                                                <th>Ingrediente</th>
                                                <th class="text-center">Cant.</th>
                                                <th class="text-end">Precio/u</th>
                                                <th class="text-end">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pedido['detalles'] as $det)
                                            <tr>
                                                <td class="small">{{ $det['nombreIngrediente'] ?? 'ID '.$det['idIngrediente'] }}</td>
                                                <td class="text-center small">{{ $det['cantidad'] }}</td>
                                                <td class="text-end small">
                                                    $ {{ number_format($det['precioUnitario'] ?? 0, 0, ',', '.') }}
                                                </td>
                                                <td class="text-end small fw-bold">
                                                    $ {{ number_format(($det['precioUnitario'] ?? 0) * ($det['cantidad'] ?? 0), 0, ',', '.') }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="total-cop text-end">
                                        <i class="fas fa-coins me-1"></i>
                                        Total: <span>COP $ {{ number_format($totalCOP, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @else
                                <p class="text-muted small fst-italic mb-3">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Ver detalle para ver ingredientes
                                </p>
                            @endif

                            {{-- Botones --}}
                            <div class="d-flex gap-2 mt-auto pt-3 border-top flex-wrap">

                                {{-- Botón Entregar — solo si NO está entregado --}}
                                @if(strtoupper($estado) !== 'ENTREGADO')
                                    <form method="POST"
                                          action="{{ route('pedidoproveedores.entregar', $pedido['idPedidoProv']) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="btn btn-sm btn-success fw-bold"
                                                onclick="return confirm('¿Confirmar entrega? Se sumará al inventario.')">
                                            <i class="fas fa-box-open me-1"></i> Entregar
                                        </button>
                                    </form>
                                @else
                                    <span class="btn btn-sm btn-outline-success disabled fw-bold">
                                        <i class="fas fa-check me-1"></i> Entregado
                                    </span>
                                @endif

                                {{-- Ver detalle --}}
                                <a href="{{ route('pedidoproveedores.show', $pedido['idPedidoProv']) }}"
                                   class="btn btn-sm btn-outline-secondary flex-grow-1">
                                    <i class="fas fa-eye me-1"></i> Detalles
                                </a>

                                {{-- Eliminar --}}
                                <form method="POST"
                                      action="{{ route('pedidoproveedores.destroy', $pedido['idPedidoProv']) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('¿Eliminar permanentemente este pedido?')">
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
                        <p class="text-muted">No se encontraron pedidos registrados.</p>
                    </div>
                @endforelse
            </div>

        </main>
    </div>
</div>

{{-- MODAL CREAR PEDIDO --}}
<div class="modal fade" id="crearModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header text-white border-0"
                 style="background-color: #5d4037; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-cart-plus me-2"></i>Registrar Nuevo Pedido
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('pedidoproveedores.store') }}">
                @csrf
                <div class="modal-body p-4">

                    {{-- Encabezado del pedido --}}
                    <div class="row g-3 mb-4 p-4 rounded-3"
                         style="background-color: #fdfbf9; border: 1px dashed #d7ccc8;">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Proveedor</label>
                            <select name="idProveedor" class="form-select" required>
                                <option value="" disabled selected>Seleccionar...</option>
                                @foreach($proveedores as $prov)
                                    <option value="{{ $prov['idProveedor'] }}">
                                        {{ $prov['nombreProv'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Número de Pedido</label>
                            <input type="number" name="numeroPedido" class="form-control"
                                   required placeholder="Ej: 1001">
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

                    {{-- Detalles de ingredientes --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0" style="color: #8d6e63;">
                            <i class="fas fa-boxes me-2"></i>Ingredientes a Solicitar
                        </h5>
                        <button type="button" id="add-detail-btn"
                                class="btn btn-sm btn-success rounded-pill px-3">
                            <i class="fas fa-plus me-1"></i> Agregar
                        </button>
                    </div>

                    <div id="detalles-container">
                        <div class="row g-2 detalle-row mb-2 p-3 bg-light rounded-3 align-items-end border">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Ingrediente</label>
                                <select name="detalles[0][idIngrediente]" class="form-select" required>
                                    <option value="" disabled selected>Seleccionar...</option>
                                    @foreach($ingredientes as $ing)
                                        <option value="{{ $ing['idIngrediente'] }}">
                                            {{ $ing['nombreIngrediente'] }}
                                            @if(!empty($ing['abreviaturaUnidad']))
                                                ({{ $ing['abreviaturaUnidad'] }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Cantidad</label>
                                <input type="number" name="detalles[0][cantidad]"
                                       class="form-control cantidad-input" required min="1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">
                                    Precio Unitario (COP)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text fw-bold">$</span>
                                    <input type="number" step="1" name="detalles[0][precioUnitario]"
                                           class="form-control precio-input" required min="0">
                                </div>
                            </div>
                            <div class="col-md-2 text-end">
                                <label class="form-label small fw-bold text-muted">Subtotal</label>
                                <div class="subtotal-display fw-bold text-success small py-2">
                                    COP $ 0
                                </div>
                                <button type="button"
                                        class="btn btn-outline-danger btn-sm w-100 remove-detail-btn"
                                        disabled>
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Total general --}}
                    <div class="text-end mt-3">
                        <div class="total-cop d-inline-block">
                            <i class="fas fa-coins me-1"></i>
                            Total Pedido: <span id="totalGeneral">COP $ 0</span>
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-0 bg-light p-3" style="border-radius: 0 0 20px 20px;">
                    <button type="button" class="btn btn-link text-muted text-decoration-none"
                            data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-crear shadow">
                        <i class="fas fa-save me-2"></i>Procesar Pedido
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ── Buscador ────────────────────────────────────────────────
    document.getElementById('pedidoSearch').addEventListener('keyup', function () {
        const term = this.value.toLowerCase();
        document.querySelectorAll('.pedido-item').forEach(item => {
            item.style.display = item.textContent.toLowerCase().includes(term) ? '' : 'none';
        });
    });

    // ── Ingredientes disponibles para el select dinámico ────────
    const ingredientesDisponibles = @json($ingredientes ?? []);

    let detailIndex = 1;
    const container = document.getElementById('detalles-container');

    // ── Agregar fila ────────────────────────────────────────────
    document.getElementById('add-detail-btn').addEventListener('click', function () {
        let options = ingredientesDisponibles.map(i =>
            `<option value="${i.idIngrediente}">
                ${i.nombreIngrediente}${i.abreviaturaUnidad ? ' (' + i.abreviaturaUnidad + ')' : ''}
            </option>`
        ).join('');

        const row = document.createElement('div');
        row.className = 'row g-2 detalle-row mb-2 p-3 bg-light rounded-3 align-items-end border';
        row.innerHTML = `
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Ingrediente</label>
                <select name="detalles[${detailIndex}][idIngrediente]" class="form-select" required>
                    <option value="" disabled selected>Seleccionar...</option>
                    ${options}
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Cantidad</label>
                <input type="number" name="detalles[${detailIndex}][cantidad]"
                       class="form-control cantidad-input" required min="1">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Precio Unitario (COP)</label>
                <div class="input-group">
                    <span class="input-group-text fw-bold">$</span>
                    <input type="number" step="1" name="detalles[${detailIndex}][precioUnitario]"
                           class="form-control precio-input" required min="0">
                </div>
            </div>
            <div class="col-md-2 text-end">
                <label class="form-label small fw-bold text-muted">Subtotal</label>
                <div class="subtotal-display fw-bold text-success small py-2">COP $ 0</div>
                <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-detail-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
        detailIndex++;
        actualizarBotonesEliminar();
    });

    // ── Eliminar fila ───────────────────────────────────────────
    container.addEventListener('click', function (e) {
        if (e.target.closest('.remove-detail-btn')) {
            e.target.closest('.detalle-row').remove();
            actualizarBotonesEliminar();
            calcularTotal();
        }
    });

    // ── Calcular subtotal por fila y total general ──────────────
    container.addEventListener('input', function (e) {
        if (e.target.classList.contains('cantidad-input') ||
            e.target.classList.contains('precio-input')) {
            const fila = e.target.closest('.detalle-row');
            const cantidad = parseFloat(fila.querySelector('.cantidad-input').value) || 0;
            const precio   = parseFloat(fila.querySelector('.precio-input').value) || 0;
            const subtotal = cantidad * precio;
            fila.querySelector('.subtotal-display').textContent =
                'COP $ ' + subtotal.toLocaleString('es-CO');
            calcularTotal();
        }
    });

    function calcularTotal() {
        let total = 0;
        document.querySelectorAll('.detalle-row').forEach(fila => {
            const cantidad = parseFloat(fila.querySelector('.cantidad-input')?.value) || 0;
            const precio   = parseFloat(fila.querySelector('.precio-input')?.value) || 0;
            total += cantidad * precio;
        });
        document.getElementById('totalGeneral').textContent =
            'COP $ ' + total.toLocaleString('es-CO');
    }

    function actualizarBotonesEliminar() {
        const filas = container.querySelectorAll('.detalle-row');
        filas.forEach(fila => {
            fila.querySelector('.remove-detail-btn').disabled = (filas.length === 1);
        });
    }
</script>
@endpush