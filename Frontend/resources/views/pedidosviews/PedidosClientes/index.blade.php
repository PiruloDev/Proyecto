@extends('layouts.app')

@section('title', 'Gestión de Pedidos')

@section('content')

<div class="container">
    <h1 class="mb-4">Listado de Pedidos</h1>

    {{-- Enlace 'Crear Pedido' usa la ruta corregida 'pedidos.create' --}}
    <a href="{{ route('pedidos.create') }}" class="btn btn-primary mb-3">
        Crear Pedido
    </a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Empleado</th>
                <th>Estado</th>
                <th>Total</th>
                <th>Ingreso</th>
                <th>Entrega</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            @foreach($pedidos as $pedido)
                <tr>
                    {{-- 1. Mostrar ID: Usamos la clave primaria correcta ID_PEDIDO --}}
                    {{-- Nota: Usaremos $pedido['ID_PEDIDO'] asumiendo que el resultado es un array --}}
                    <td>{{ $pedido['ID_PEDIDO'] }}</td> 
                    
                    {{-- Usar mayúsculas o minúsculas depende de cómo devuelve los datos tu consulta/Modelo --}}
                    <td>{{ $pedido['ID_CLIENTE'] }}</td> 
                    <td>{{ $pedido['ID_EMPLEADO'] }}</td> 
                    <td>{{ $pedido['ID_ESTADO_PEDIDO'] }}</td> 
                    <td>{{ $pedido['TOTAL_PRODUCTO'] }}</td> 
                    <td>{{ $pedido['FECHA_INGRESO'] }}</td> 
                    <td>{{ $pedido['FECHA_ENTREGA'] }}</td>

                    <td>
                        {{-- 2. CORRECCIÓN EDICIÓN: Usamos $pedido['ID_PEDIDO'] para el parámetro de ruta --}}
                        <a href="{{ route('pedidos.edit', $pedido['ID_PEDIDO']) }}" class="btn btn-warning btn-sm">
                            Editar
                        </a>

                        {{-- 3. CORRECCIÓN ELIMINAR: Usamos $pedido['ID_PEDIDO'] para el parámetro de ruta --}}
                        <form action="{{ route('pedidos.destroy', $pedido['ID_PEDIDO']) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
</div>

@endsection