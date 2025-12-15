{{-- resources/views/inventarioviews/ingredientes/inventario.blade.php --}}

@extends('layouts.app')

@section('title', 'Inventario de Ingredientes')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="{{ asset('css/variables.css') }}" rel="stylesheet">
<link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    .inventory-card {
        transition: all 0.3s ease;
        border-left: 4px solid var(--panaderia-marron-principal);
    }
    .inventory-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1) !important;
    }
    .stock-badge {
        font-size: 1.5rem;
        font-weight: 600;
    }
    .low-stock {
        border-left-color: #dc3545 !important;
    }
    .medium-stock {
        border-left-color: #ffc107 !important;
    }
    .good-stock {
        border-left-color: #28a745 !important;
    }
    .ingredient-icon {
        font-size: 2rem;
        opacity: 0.7;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        
        {{-- Sidebar --}}
        @include('components.admin-sidebar')
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
            
            {{-- Header --}}
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-boxes-stacked me-2" style="color: var(--panaderia-marron-principal);"></i>
                    Inventario de Ingredientes
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('ingredientes.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-list me-1"></i> Vista Completa
                    </a>
                    <a href="{{ route('ingredientes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Agregar Ingrediente
                    </a>
                </div>
            </div>

            {{-- Mensajes de alerta --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Estadísticas Rápidas --}}
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Total Ingredientes</p>
                                    <h3 class="mb-0">{{ count($ingredientes) }}</h3>
                                </div>
                                <i class="fas fa-cubes fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Stock Bajo</p>
                                    <h3 class="mb-0 text-danger">
                                        {{ collect($ingredientes)->filter(fn($i) => $i['cantidadIngrediente'] < 10)->count() }}
                                    </h3>
                                </div>
                                <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Stock Disponible</p>
                                    <h3 class="mb-0 text-success">
                                        {{ collect($ingredientes)->filter(fn($i) => $i['cantidadIngrediente'] >= 10)->count() }}
                                    </h3>
                                </div>
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Buscador --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control" placeholder="Buscar ingrediente...">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary active" onclick="filterStock('all')">
                            <i class="fas fa-filter me-1"></i> Todos
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="filterStock('low')">
                            Stock Bajo
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="filterStock('good')">
                            Stock Bueno
                        </button>
                    </div>
                </div>
            </div>

            {{-- Lista de Ingredientes en Cards --}}
            @if (empty($ingredientes))
                <div class="alert alert-info text-center py-5">
                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                    <h4>No hay ingredientes registrados</h4>
                    <p>Comienza agregando tu primer ingrediente al inventario.</p>
                    <a href="{{ route('ingredientes.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-2"></i>Agregar Ingrediente
                    </a>
                </div>
            @else
                <div class="row g-3" id="ingredientesContainer">
                    @foreach ($ingredientes as $ingrediente)
                        @php
                            $cantidad = $ingrediente['cantidadIngrediente'] ?? 0;
                            $stockClass = $cantidad < 10 ? 'low-stock' : ($cantidad < 50 ? 'medium-stock' : 'good-stock');
                            $stockBadgeClass = $cantidad < 10 ? 'bg-danger' : ($cantidad < 50 ? 'bg-warning' : 'bg-success');
                        @endphp
                        
                        <div class="col-md-6 col-lg-4 col-xl-3 ingredient-item" data-stock="{{ $cantidad }}" data-name="{{ strtolower($ingrediente['nombreIngrediente']) }}">
                            <div class="card inventory-card border-0 shadow-sm h-100 {{ $stockClass }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <i class="fas fa-pepper-hot ingredient-icon" style="color: var(--panaderia-marron-principal);"></i>
                                        <span class="badge {{ $stockBadgeClass }} stock-badge">{{ number_format($cantidad, 1) }}</span>
                                    </div>
                                    
                                    <h5 class="card-title mb-2">{{ $ingrediente['nombreIngrediente'] }}</h5>
                                    <p class="text-muted small mb-3">ID: {{ $ingrediente['idIngrediente'] }}</p>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Unidades disponibles</small>
                                <div>
                                    <button type="button" class="btn btn-sm btn-success btn-ingreso-stock" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#ingresoStockModal"
                                            data-id="{{ $ingrediente['idIngrediente'] }}" 
                                            data-name="{{ $ingrediente['nombreIngrediente'] }}">
                                        <i class="fas fa-truck-ramp-box me-1"></i> Reponer
                                    </button>
                                    <a href="{{ route('ingredientes.index') }}" class="btn btn-sm btn-outline-primary ms-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </main>
    </div>
</div>

<div class="modal fade" id="ingresoStockModal" tabindex="-1" aria-labelledby="ingresoStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="ingresoStockModalLabel">
                    <i class="fas fa-truck-ramp-box me-2"></i> Reponer Stock
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formIngresoStock">
                <div class="modal-body">
                    <input type="hidden" name="ingrediente_id" id="ingredienteId">
                    <p class="text-muted mb-4">Ingresando stock para: 
                        <strong id="ingredienteNombreModal"></strong>
                    </p>

                    <div class="mb-3">
                        <label for="cantidadIngresada" class="form-label">Cantidad a Ingresar</label>
                        <input type="number" step="0.01" min="0.01" class="form-control form-control-lg" id="cantidadIngresada" name="cantidadIngresada" required placeholder="Ej: 15.5">
                        <div class="invalid-feedback" id="cantidadIngresadaFeedback">
                            Ingrese una cantidad positiva.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="btnGuardarStock">
                        <i class="fas fa-save me-1"></i> Registrar Ingreso
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



@push('scripts')
<script>
    // Búsqueda en tiempo real
    document.getElementById('searchInput').addEventListener('keyup', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const items = document.querySelectorAll('.ingredient-item');
        
        items.forEach(item => {
            const name = item.getAttribute('data-name');
            if (name.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Filtro por stock
    function filterStock(type) {
        const items = document.querySelectorAll('.ingredient-item');
        const buttons = document.querySelectorAll('.btn-group button');
        
        // Actualizar botones activos
        buttons.forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        
        items.forEach(item => {
            const stock = parseFloat(item.getAttribute('data-stock'));
            
            if (type === 'all') {
                item.style.display = '';
            } else if (type === 'low' && stock < 10) {
                item.style.display = '';
            } else if (type === 'good' && stock >= 10) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    const ingresoStockModal = document.getElementById('ingresoStockModal');
    const formIngresoStock = document.getElementById('formIngresoStock');
    const ingredienteIdInput = document.getElementById('ingredienteId');
    const ingredienteNombreModal = document.getElementById('ingredienteNombreModal');
    const cantidadIngresadaInput = document.getElementById('cantidadIngresada');
    const cantidadIngresadaFeedback = document.getElementById('cantidadIngresadaFeedback');
    const btnGuardarStock = document.getElementById('btnGuardarStock');

    // 1. Lógica para cargar datos al abrir el modal
    ingresoStockModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget; // Botón que disparó el modal
        
        // Extraer info de los data-attributes
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        
        // Insertar datos en el modal
        ingredienteIdInput.value = id;
        ingredienteNombreModal.textContent = name;
        
        // Limpiar el estado de validación previo
        cantidadIngresadaInput.value = '';
        cantidadIngresadaInput.classList.remove('is-invalid');
        btnGuardarStock.disabled = false;
        btnGuardarStock.innerHTML = '<i class="fas fa-save me-1"></i> Registrar Ingreso';
    });

    // 2. Lógica para manejar el envío del formulario (AJAX)
    formIngresoStock.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const id = ingredienteIdInput.value;
        const cantidad = cantidadIngresadaInput.value;
        const url = `{{ url('/ingredientes') }}/${id}/ingresar-stock`;
        
        // Validación básica en JS (previene envío con campo vacío o negativo)
        if (cantidad === '' || parseFloat(cantidad) <= 0) {
            cantidadIngresadaInput.classList.add('is-invalid');
            cantidadIngresadaFeedback.textContent = 'La cantidad debe ser un número positivo (ej: 1.5).';
            return;
        } else {
            cantidadIngresadaInput.classList.remove('is-invalid');
        }

        // Deshabilitar botón y mostrar carga
        btnGuardarStock.disabled = true;
        btnGuardarStock.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Procesando...';

        
        // Petición AJAX (Fetch API)
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                // Importante: Token de seguridad CSRF de Laravel
                'X-CSRF-TOKEN': '{{ csrf_token() }}' 
            },
            body: JSON.stringify({
                cantidadIngresada: cantidad
            })
        })
        .then(response => {
            // Manejar la respuesta HTTP (incluyendo 500 del controlador)
            if (!response.ok) {
                // Si hay error, intentar leer el JSON del controlador
                return response.json().then(data => {
                    throw new Error(data.error || 'Error desconocido en el servidor.');
                });
            }
            return response.json();
        })
        .then(data => {
            // Éxito: Redirigir o recargar para ver la nueva cantidad de stock
            const successMessage = data.message || 'Stock actualizado correctamente.';
            
            // Recargamos la página con el mensaje de éxito en la URL para que se muestre arriba
            window.location.href = `{{ url()->current() }}?success=${encodeURIComponent(successMessage)}`;
            
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Revertir el estado del botón
            btnGuardarStock.disabled = false;
            btnGuardarStock.innerHTML = '<i class="fas fa-save me-1"></i> Registrar Ingreso';
            
            let errorMessage = error.message || 'Error de conexión desconocido.';
            
            // Mostrar error en el formulario (y quitar el spinner si lo hay)
            cantidadIngresadaInput.classList.add('is-invalid');
            cantidadIngresadaFeedback.textContent = errorMessage;
        });
    });

    // 3. Lógica para manejar el mensaje de éxito/error después de la redirección
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const successMessage = urlParams.get('success');

        if (successMessage) {
            // 1. Eliminar el parámetro 'success' de la URL para limpieza
            window.history.replaceState(null, '', window.location.pathname);
            
            // 2. Agregar alerta de éxito al inicio del contenido principal
            const alertHtml = `
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>${decodeURIComponent(successMessage)}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            // Aseguramos que el mensaje se añada en la sección de alertas, asumiendo que está dentro del main content
            const mainContent = document.querySelector('.container-fluid');
            if(mainContent) {
                mainContent.querySelector('.row').insertAdjacentHTML('afterbegin', alertHtml);
            }
        }
    });



</script>
@endpush

@endsection