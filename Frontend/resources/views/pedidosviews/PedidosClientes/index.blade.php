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
                    {{-- ID del pedido --}}
                    <td>{{ $pedido->ID_PEDIDO }}</td>

                    {{-- Nombre del Cliente --}}
                    <td>{{ $pedido->cliente->NOMBRE_CLI ?? 'Sin cliente' }}</td>

                    {{-- Empleado --}}
                    <td>{{ $pedido->ID_EMPLEADO }}</td>

                    {{-- Estado --}}
                    <td>{{ $pedido->estado->NOMBRE_ESTADO ?? 'Sin estado' }}</td>

                    {{-- Total --}}
                    <td>{{ $pedido->TOTAL_PRODUCTO }}</td>

                    {{-- Fechas --}}
                    <td>{{ $pedido->FECHA_INGRESO }}</td>
                    <td>{{ $pedido->FECHA_ENTREGA }}</td>

                    <td>
                        {{-- Botón Editar --}}
                        <a href="{{ route('pedidos.edit', $pedido->ID_PEDIDO) }}" class="btn btn-warning btn-sm">
                            Editar
                        </a>

                        {{-- Botón Eliminar --}}
                        <form action="{{ route('pedidos.destroy', $pedido->ID_PEDIDO) }}" method="POST" class="d-inline">
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
