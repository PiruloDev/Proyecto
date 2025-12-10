@extends('layouts.app')

@section('title', 'Gestión de Pedidos')

@section('content')

<div class="container">
    <h1 class="mb-4">Listado de Pedidos</h1>

    <a href="{{ route('pedidos.create') }}" class="btn btn-primary mb-3">
        Crear Pedido
    </a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
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
            @foreach($pedidos as $pedido)
                <tr>
                    {{-- 🛑 CORRECCIÓN: Usar las claves EXACTAS del JSON --}}
                    <td>{{ $pedido['id_PEDIDO'] }}</td> 
                    <td>{{ $pedido['id_CLIENTE'] }}</td>
                    <td>{{ $pedido['id_EMPLEADO'] }}</td>
                    <td>{{ $pedido['id_ESTADO_PEDIDO'] }}</td>
                    <td>{{ $pedido['total_PRODUCTO'] }}</td>
                    <td>{{ $pedido['fecha_INGRESO'] }}</td>
                    <td>{{ $pedido['fecha_ENTREGA'] }}</td>

                    <td>
                        <a href="{{ route('pedidos.edit', $pedido['id_PEDIDO']) }}" class="btn btn-warning btn-sm">
                            Editar
                        </a>

                        <form action="{{ route('pedidos.destroy', $pedido['id_PEDIDO']) }}" method="POST" class="d-inline">
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