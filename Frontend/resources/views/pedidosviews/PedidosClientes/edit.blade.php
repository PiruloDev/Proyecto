@extends('layouts.app')

@section('title', 'Editar Pedido')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        
        {{-- LÓGICA CONDICIONAL DEL SIDEBAR --}}
        @php
            $esAdmin = Auth::check() && Auth::user()->rol === 'admin'; 
        @endphp

        @if ($esAdmin)
            @include('components.admin-sidebar') 
        @else
            @include('components.employee-sidebar') 
        @endif
        
        {{-- CONTENIDO PRINCIPAL --}}
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
            
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                {{-- CAMBIO AQUÍ: $pedido['id_PEDIDO'] --}}
                <h1 class="h2">Editar Pedido #{{ $pedido['id_PEDIDO'] }}</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('pedidos.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list"></i> Volver al Listado
                    </a>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger my-3">
                    <p>Por favor, corrige los siguientes errores:</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="p-4 bg-white shadow-sm rounded-3">
                
                {{-- CAMBIO AQUÍ: $pedido['id_PEDIDO'] --}}
                <form action="{{ route('pedidos.update', $pedido['id_PEDIDO']) }}" method="POST" class="row g-3">
                    @csrf
                    @method('PUT') 
                    
                    <div class="col-md-6">
                        <label for="ID_CLIENTE" class="form-label">ID Cliente</label>
                        {{-- CAMBIO AQUÍ: $pedido['id_CLIENTE'] --}}
                        <input type="number" class="form-control" id="ID_CLIENTE" name="ID_CLIENTE" 
                               value="{{ old('ID_CLIENTE', $pedido['id_CLIENTE']) }}" required> 
                        @error('ID_CLIENTE')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="ID_EMPLEADO" class="form-label">ID Empleado</label>
                        {{-- CAMBIO AQUÍ: $pedido['id_EMPLEADO'] --}}
                        <input type="number" class="form-control" id="ID_EMPLEADO" name="ID_EMPLEADO" 
                               value="{{ old('ID_EMPLEADO', $pedido['id_EMPLEADO']) }}" required>
                        @error('ID_EMPLEADO')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="ID_ESTADO_PEDIDO" class="form-label">ID Estado Pedido</label>
                        {{-- CAMBIO AQUÍ: $pedido['id_ESTADO_PEDIDO'] --}}
                        <input type="number" class="form-control" id="ID_ESTADO_PEDIDO" name="ID_ESTADO_PEDIDO" 
                               value="{{ old('ID_ESTADO_PEDIDO', $pedido['id_ESTADO_PEDIDO']) }}" required>
                        @error('ID_ESTADO_PEDIDO')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="FECHA_ENTREGA" class="form-label">Fecha de Entrega (Opcional)</label>
                        {{-- CAMBIO AQUÍ: $pedido['fecha_ENTREGA'] --}}
                        <input type="date" class="form-control" id="FECHA_ENTREGA" name="FECHA_ENTREGA" 
                               value="{{ old('FECHA_ENTREGA', $pedido['fecha_ENTREGA']) }}">
                        @error('FECHA_ENTREGA')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="TOTAL_PRODUCTO" class="form-label">Total Producto</label>
                        {{-- CAMBIO AQUÍ: $pedido['total_PRODUCTO'] --}}
                        <input type="number" step="0.01" class="form-control" id="TOTAL_PRODUCTO" name="TOTAL_PRODUCTO" 
                               value="{{ old('TOTAL_PRODUCTO', $pedido['total_PRODUCTO']) }}" required>
                        @error('TOTAL_PRODUCTO')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 mt-4 d-flex justify-content-start">
                        <button type="submit" class="btn btn-warning me-2">
                            <i class="fas fa-save"></i> Actualizar Pedido
                        </button>
                        <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </section>
        </main>
    </div>
</div>
@endsection