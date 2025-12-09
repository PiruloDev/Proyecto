@extends('layouts.app')

@section('title', 'Crear Pedido')

@section('content')
<div class="container">

    <h1 class="mb-4">Crear Nuevo Pedido</h1>

    
    <form action="{{ route('pedidos.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>ID Cliente</label>
            <input type="number" class="form-control" name="ID_CLIENTE" required> 
        </div>

        <div class="mb-3">
            <label>ID Empleado</label>
            <input type="number" class="form-control" name="ID_EMPLEADO" required>
        </div>

        <div class="mb-3">
            <label>ID Estado Pedido</label>
            <input type="number" class="form-control" name="ID_ESTADO_PEDIDO" required>
        </div>

        <div class="mb-3">
            <label>Total Producto</label>
            <input type="number" step="0.01" class="form-control" name="TOTAL_PRODUCTO" required>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>

    </form>

</div>
@endsection