@extends('layouts.app')

@section('title', 'Editar Pedido')

@section('content')
<div class="container">

    <h1 class="mb-4">Editar Pedido</h1>

    {{-- Usar la clave correcta para la URL: id_PEDIDO --}}
    <form action="{{ route('pedidos.update', $pedido['id_PEDIDO']) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>ID Cliente</label>
            {{-- Usar la clave correcta: id_CLIENTE --}}
            <input type="number" class="form-control" name="ID_CLIENTE" value="{{ $pedido['id_CLIENTE'] }}" required>
        </div>

        <div class="mb-3">
            <label>ID Empleado</label>
            {{-- Usar la clave correcta: id_EMPLEADO --}}
            <input type="number" class="form-control" name="ID_EMPLEADO" value="{{ $pedido['id_EMPLEADO'] }}" required>
        </div>

        <div class="mb-3">
            <label>ID Estado Pedido</label>
            {{-- Usar la clave correcta: id_ESTADO_PEDIDO --}}
            <input type="number" class="form-control" name="ID_ESTADO_PEDIDO" value="{{ $pedido['id_ESTADO_PEDIDO'] }}" required>
        </div>

        <div class="mb-3">
            <label>Total Producto</label>
            {{-- Usar la clave correcta: total_PRODUCTO --}}
            <input type="number" step="0.01" class="form-control" name="TOTAL_PRODUCTO" value="{{ $pedido['total_PRODUCTO'] }}" required>
        </div>

        <button type="submit" class="btn btn-warning">Actualizar</button>
    </form>

</div>
@endsection