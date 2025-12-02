@extends('layouts.app') 

@section('title', 'Gestión de Proveedores')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/stylemoduloinv.css') }}">
@endpush

@section('content')

<div class="container-fluid">
    <div class="row g-0">
        {{-- Sidebar Reutilizable --}}
        @include('partials.sidebar-inventario')

        <div class="col-md-9 col-lg-10 main-content">
            <h1>Gestión de Proveedores</h1>
            <p class="lead">Aquí podrás gestionar la información de los proveedores.</p>

            <section id="listado">
                <h2>Listado de Proveedores</h2>
                <div class="alert alert-warning">
                    Funcionalidad de listado de proveedores en desarrollo. Migrar lógica desde `proveedoresController.php`.
                </div>
            </section>
        </div>
    </div>
</div>
@endsection