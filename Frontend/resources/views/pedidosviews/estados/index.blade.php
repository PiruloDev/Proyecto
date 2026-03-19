@extends('layouts.app')

@section('content')
<div class="container">
   <h2 class="mb-4">Estados de Pedido</h2>

   <a href="{{ route('estados.create') }}" class="btn btn-panaderia-action mb-3">
    Crear nuevo estado
    </a>
    
    {{-- Mensajes de éxito y error --}}
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
                <th>Nombre del Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody> 
            @foreach($estados as $estado)
                <tr>
                    {{-- 1. Celda ID --}}
                    <td>{{ $estado['id_ESTADO_PEDIDO'] }}</td>
                    
                    {{-- 2. Celda Nombre del Estado --}}
                    <td>{{ $estado['nombre_ESTADO'] }}</td>
                    
                    {{-- 3. Celda Acciones: Usamos d-flex para controlar el contenedor --}}
                    <td class="d-flex align-items-center">
                        
                        {{-- Botón EDITAR (mx-1 da un pequeño margen a la derecha) --}}
                        <a href="{{ route('estados.edit', $estado['id_ESTADO_PEDIDO']) }}" class="btn btn-warning btn-sm mx-1">Editar</a>

                        {{-- Formulario ELIMINAR: Usamos d-inline para que no ocupe todo el ancho y se pegue al botón Editar --}}
                        <form action="{{ route('estados.destroy', $estado['id_ESTADO_PEDIDO']) }}" method="POST" class="d-inline m-0"> 
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('¿Eliminar este estado?')" class="btn btn-danger btn-sm">
                                Eliminar
                            </button>
                        </form>
                    </td> 
                </tr>
            @endforeach
            
            {{-- Mensaje de tabla vacía --}}
            @if(count($estados) === 0)
                <tr>
                    <td colspan="3">No hay estados registrados.</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
@endsection