@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="alert alert-info">
        <h4>Funcionalidad disponible en el Listado</h4>
        <p>Esta acción ahora se realiza directamente mediante ventanas modales en la página principal de órdenes de salida.</p>
        <a href="{{ route('ordenes.salida.index') }}" class="btn btn-primary">Volver al listado</a>
    </div>
</div>
@endsection
