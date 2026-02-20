@extends('layouts.app')

@section('title', 'Gestión de Pedidos')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body, html { height: 100%; overflow: hidden; }
        .container-fluid, .row { height: 100vh; }
        .main-content {
            height: 100vh;
            overflow-y: auto;
            background-color: #fdfaf6;
            padding: 0 !important;
            display: flex;
            flex-direction: column;
        }
        .sticky-header-section {
            position: sticky;
            top: 0;
            z-index: 1020; 
            background-color: #fdfaf6;
            padding: 1.5rem 2rem 0 2rem;
            border-bottom: 1px solid #dee2e6;
        }
        .table-responsive { padding: 1rem 2rem 2rem 2rem; }
        .table thead th {
            position: sticky;
            top: 0; 
            z-index: 1010;
            background-color: #ffffff !important;
            border-bottom: 2px solid #dee2e6 !important;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        
        @php
            $esAdmin = Auth::check() && Auth::user()->rol === 'admin'; 
        @endphp

        @if ($esAdmin)
            @include('components.admin-sidebar') 
        @else
            @include('components.employee-sidebar') 
        @endif
        
        <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
            
            <div class="sticky-header-section">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
                    <h1 class="h2">Listado de Pedidos</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        {{-- CAMBIO 1: Botón ahora es un link a la vista create --}}
                        <a href="{{ route('pedidos.create') }}" class="btn btn-primary me-2" style="background: #a67c52; border: none;">
                            <i class="fas fa-plus"></i> Crear Pedido
                        </a>
                        <a href="{{ route('pedidos.index') }}" class="btn btn-outline-primary bg-white shadow-sm">
                            <i class="fas fa-sync"></i> Recargar
                        </a>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif  
                
                <h3 class="mb-3 text-secondary">Pedidos Registrados</h3>
            </div>

            <section id="listado-pedidos">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle shadow-sm rounded-3">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Empleado</th>
                                <th>Estado</th>
                                <th>Total</th>
                                <th>Entrega</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($pedidos as $pedido)
                                <tr>
                                    <td>{{ $pedido['id_PEDIDO'] }}</td>
                                    <td>{{ $pedido['nombre_cliente'] ?? 'ID: '.$pedido['id_CLIENTE'] }}</td>
                                    <td>{{ $pedido['nombre_empleado'] ?? 'ID: '.$pedido['id_EMPLEADO'] }}</td>
                                    <td>
                                        <span class="badge bg-info text-dark">
                                            {{ $pedido['nombre_estado'] ?? 'Estado: '.$pedido['id_ESTADO_PEDIDO'] }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-success">${{ number_format($pedido['total_PRODUCTO'] ?? 0, 0, ',', '.') }}</td>
                                    <td>{{ $pedido['fecha_ENTREGA'] ?? 'N/A' }}</td>

                                    <td class="text-center text-nowrap">
                                        {{-- CAMBIO 2: El botón de editar ahora es un link a la vista edit --}}
                                        <a href="{{ route('pedidos.edit', $pedido['id_PEDIDO']) }}" 
                                           class="btn btn-warning btn-sm me-1 shadow-sm">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>

                                        <form method="POST" action="{{ route('pedidos.destroy', $pedido['id_PEDIDO']) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm shadow-sm" onclick="return confirm('¿Está seguro de eliminar este pedido?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No hay pedidos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</div>
@endsection

{{-- CAMBIO 3: Eliminamos todo el JS del Modal que ya no se usa --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log("Listado de pedidos cargado.");
    });
</script>
@endpush
