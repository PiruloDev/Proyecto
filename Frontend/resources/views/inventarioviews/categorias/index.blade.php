@extends('layouts.app') 

@section('title', 'Gestión de Categorías')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/stylemoduloinv.css') }}">
@endpush

@section('content')

<div class="container-fluid">
    <div class="row g-0">
        {{-- Sidebar Reutilizable --}}
        @include('partials.sidebar-inventario')

        <div class="col-md-9 col-lg-10 main-content">
            <h1>Gestión de Categorías</h1>
            <p class="lead">Aquí podrás listar, crear, editar y eliminar categorías de ingredientes.</p>

            {{-- Aquí irá la lógica y el HTML para el listado y formularios de Categorías --}}
            <section id="listado">
                <h2>Listado de Categorías</h2>
                <div class="alert alert-warning">
                    Funcionalidad de listado de categorías en desarrollo. Migrar lógica desde `categoriaController.php`.
                </div>
            </section>
        </div>
    </div>
</div>
@endsection