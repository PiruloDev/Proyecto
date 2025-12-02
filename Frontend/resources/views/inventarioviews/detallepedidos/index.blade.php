@extends('layouts.app') 

@section('title', 'Gestión de Detalle de Pedidos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/stylemoduloinv.css') }}">
@endpush

@section('content')

<div class="container-fluid">
    <div class="row g-0">
        {{-- Sidebar Reutilizable --}}
        @include('partials.sidebar-inventario')

        <div class="col-md-9 col-lg-10 main-content">
            <h1>Gestión de Detalle de Pedidos</h1>
            <p class="lead">Aquí podrás revisar los detalles de los pedidos de ingredientes.</p>

            <section id="listado">
                <h2>Detalle de Pedidos</h2>
                <div class="alert alert-warning">
                    Funcionalidad de detalle de pedidos en desarrollo. Migrar lógica desde `detallePedidoController.php`.
                </div>
            </section>
        </div>
    </div>
</div>
@endsection