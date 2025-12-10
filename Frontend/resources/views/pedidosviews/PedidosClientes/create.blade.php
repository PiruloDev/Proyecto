@extends('layouts.app')

@section('title', 'Crear Pedido')

@section('content')
<div class="container">

    <h1 class="mb-4">Crear Nuevo Pedido</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('pedidos.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="ID_CLIENTE" class="form-label">ID Cliente</label>
            <input type="number" class="form-control" id="ID_CLIENTE" name="ID_CLIENTE" value="{{ old('ID_CLIENTE') }}" required> 
            @error('ID_CLIENTE')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="ID_EMPLEADO" class="form-label">ID Empleado</label>
            <input type="number" class="form-control" id="ID_EMPLEADO" name="ID_EMPLEADO" value="{{ old('ID_EMPLEADO') }}" required>
            @error('ID_EMPLEADO')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="ID_ESTADO_PEDIDO" class="form-label">ID Estado Pedido</label>
            <input type="number" class="form-control" id="ID_ESTADO_PEDIDO" name="ID_ESTADO_PEDIDO" value="{{ old('ID_ESTADO_PEDIDO') }}" required>
            @error('ID_ESTADO_PEDIDO')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="FECHA_ENTREGA" class="form-label">Fecha de Entrega (Opcional)</label>
            <input type="date" class="form-control" id="FECHA_ENTREGA" name="FECHA_ENTREGA" value="{{ old('FECHA_ENTREGA') }}">
            @error('FECHA_ENTREGA')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="TOTAL_PRODUCTO" class="form-label">Total Producto</label>
            <input type="number" step="0.01" class="form-control" id="TOTAL_PRODUCTO" name="TOTAL_PRODUCTO" value="{{ old('TOTAL_PRODUCTO') }}" required>
            @error('TOTAL_PRODUCTO')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">Cancelar</a>

    </form>

</div>
@endsection