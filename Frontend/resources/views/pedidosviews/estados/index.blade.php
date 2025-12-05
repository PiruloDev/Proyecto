@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Estados de Pedido</h2>

    <a href="{{ route('estados.create') }}" class="btn btn-primary mb-3">
        Crear nuevo estado
    </a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre del Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            @forelse($estados as $estado)
                <tr>
                    <td>{{ $estado->ID_ESTADO_PEDIDO }}</td>
                    <td>{{ $estado->NOMBRE_ESTADO }}</td>
                    <td>
                        <a href="{{ route('estados.edit', $estado->ID_ESTADO_PEDIDO) }}" class="btn btn-warning btn-sm">Editar</a>

                        <form action="{{ route('estados.destroy', $estado->ID_ESTADO_PEDIDO) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('¿Eliminar este estado?')" class="btn btn-danger btn-sm">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No hay estados registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
