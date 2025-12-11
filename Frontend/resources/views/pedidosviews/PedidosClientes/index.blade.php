@extends('layouts.app')

@section('title', 'Gestión de Pedidos')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet"> 
    {{-- Dependiendo del rol, podrías necesitar uno u otro, pero dashboard-admin.css suele tener la estructura base --}}
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        
        {{-- ================================================= --}}
        {{-- ✅ LÓGICA CONDICIONAL DEL SIDEBAR (CORREGIDA) --}}
        {{-- ================================================= --}}
        @php
            // La lógica para determinar el rol del usuario autenticado
            $esAdmin = Auth::check() && Auth::user()->rol === 'admin'; 
        @endphp

        @if ($esAdmin)
            {{-- Incluye el sidebar del Administrador --}}
            @include('components.admin-sidebar') 
        @else
            {{-- Incluye el sidebar del Empleado (o rol por defecto) --}}
            @include('components.employee-sidebar') 
        @endif
        
        {{-- CONTENIDO PRINCIPAL --}}
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
            
            {{-- Título y Botones de Acción --}}
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2">Listado de Pedidos</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    {{-- Botón Crear Pedido --}}
                    <a href="{{ route('pedidos.create') }}" class="btn btn-primary me-2">
                        <i class="fas fa-plus"></i> Crear Pedido
                    </a>
                    {{-- Botón Recargar --}}
                    <a href="{{ route('pedidos.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-sync"></i> Recargar Listado
                    </a>
                </div>
            </div>

            {{-- MENSAJES DE SESIÓN --}}
            @if (session('success'))
                <div class="alert alert-success my-3">{{ session('success') }}</div>
            @endif  

            @if (session('error'))
                <div class="alert alert-danger my-3">{{ session('error') }}</div>
            @endif
            
            <section id="listado-pedidos" class="mb-5">
                <h3 class="mb-3 text-secondary">Pedidos Registrados</h3>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle shadow-sm rounded-3">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Cliente ID</th>
                                <th>Empleado ID</th>
                                <th>Estado ID</th>
                                <th>Total</th>
                                <th>Ingreso</th>
                                <th>Entrega</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($pedidos as $pedido)
                                <tr>
                                    <td>{{ $pedido['id_PEDIDO'] }}</td>
                                    <td>{{ $pedido['id_CLIENTE'] }}</td>
                                    <td>{{ $pedido['id_EMPLEADO'] }}</td>
                                    <td>{{ $pedido['id_ESTADO_PEDIDO'] }}</td>
                                    <td>${{ number_format($pedido['total_PRODUCTO'] ?? 0, 2) }}</td>
                                    <td>{{ $pedido['fecha_INGRESO'] ?? 'N/A' }}</td>
                                    <td>{{ $pedido['fecha_ENTREGA'] ?? 'N/A' }}</td>

                                    <td class="text-nowrap">
                                        <a href="{{ route('pedidos.edit', $pedido['id_PEDIDO']) }}" 
                                            class="btn btn-warning btn-sm me-1">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>

                                        <form method="POST"
                                            action="{{ route('pedidos.destroy', $pedido['id_PEDIDO']) }}"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('¿Está seguro de eliminar este pedido?')">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No hay pedidos registrados.</td>
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