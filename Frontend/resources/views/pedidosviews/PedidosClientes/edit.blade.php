@extends('layouts.app')

@section('title', 'Editar Pedido')

@section('content')
<div class="container">

    <h1 class="mb-4">Editar Pedido</h1>

    {{-- CORRECCIÓN 1: Cambiar ruta a 'pedidos.update' --}}
    {{-- CORRECCIÓN 2: Cambiar parámetro a $pedido['ID_PEDIDO'] --}}
    <form action="{{ route('pedidos.update', $pedido['ID_PEDIDO']) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>ID Cliente</label>
            {{-- Usamos las claves de la base de datos en mayúsculas --}}
            <input type="number" class="form-control" name="ID_CLIENTE" value="{{ $pedido['ID_CLIENTE'] }}" required>
        </div>

        <div class="mb-3">
            <label>ID Empleado</label>
            <input type="number" class="form-control" name="ID_EMPLEADO" value="{{ $pedido['ID_EMPLEADO'] }}" required>
        </div>

        <div class="mb-3">
            <label>ID Estado Pedido</label>
            <input type="number" class="form-control" name="ID_ESTADO_PEDIDO" value="{{ $pedido['ID_ESTADO_PEDIDO'] }}" required>
        </div>

        <div class="mb-3">
            <label>Total Producto</label>
            <input type="number" step="0.01" class="form-control" name="TOTAL_PRODUCTO" value="{{ $pedido['TOTAL_PRODUCTO'] }}" required>
        </div>

        <button type="submit" class="btn btn-warning">Actualizar</button>
    </form>

</div>
@endsection