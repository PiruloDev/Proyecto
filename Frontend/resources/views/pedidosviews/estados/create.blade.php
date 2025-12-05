@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Crear Estado de Pedido</h2>

    <form action="{{ route('estados.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nombre del Estado</label>
            <input type="text" name="NOMBRE_ESTADO" class="form-control" required>
        </div>

        <button class="btn btn-success" type="submit">Guardar</button>
        <a href="{{ route('estados.index') }}" class="btn btn-secondary">Volver</a>
    </form>
</div>
@endsection
