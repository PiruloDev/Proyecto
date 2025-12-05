@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Editar Estado de Pedido</h2>

    <form action="{{ route('estados.update', $estado->ID_ESTADO_PEDIDO) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nombre del Estado</label>
            <input type="text" name="NOMBRE_ESTADO" value="{{ $estado->NOMBRE_ESTADO }}" class="form-control" required>
        </div>

        <button class="btn btn-primary" type="submit">Actualizar</button>
        <a href="{{ route('estados.index') }}" class="btn btn-secondary">Volver</a>
    </form>
</div>
@endsection
