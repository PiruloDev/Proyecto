{{-- resources/views/dashboard/inventario.blade.php --}}

@extends('layouts.app') 

@section('title', 'Dashboard - Módulo Inventario')

@push('styles')
{{-- Usamos tus archivos existentes --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="{{ asset('css/variables.css') }}" rel="stylesheet">
<link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet">

<style>
    /* Ajustes específicos para que las cards de este módulo se vean uniformes */
    .inventory-icon {
        font-size: 2.5rem;
        color: var(--panaderia-marron-principal);
        margin-bottom: 1rem;
        display: block;
    }
    
    .inventory-card {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        min-height: 250px;
        text-align: center;
    }

    /* Forzamos que el botón esté siempre abajo */
    .inventory-card .btn {
        margin-top: auto;
    }

    .section-header {
        color: var(--panaderia-marron-oscuro);
        font-family: var(--panaderia-font-primary);
        font-weight: 700;
        border-bottom: 2px solid var(--panaderia-beige-oscuro);
        padding-bottom: 0.5rem;
    }

    /* Eliminar scroll horizontal */
    html, body { overflow-x: hidden; }
    .container-fluid { overflow-x: hidden; max-width: 100%; }
    .main-content { overflow-x: hidden; }
    .content-wrapper { overflow-x: hidden; }
    .content-wrapper .row { --bs-gutter-x: 1.5rem; margin-right: 0; margin-left: 0; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        
        {{-- Sidebar Component (Usa tus clases globales) --}}
        @include('components.admin-sidebar') 
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4 main-content">
            <div class="content-wrapper">
                {{-- Encabezado --}}
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                    <h1 class="dashboard-page-title">
                        Módulo de Inventario
                    </h1>
                </div>

                {{-- Grid de Tarjetas usando .glass-card de tu CSS global --}}
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4 mb-5">
                    
                    @php
                        $modulos = [
                            ['route' => 'ingredientes.index', 'icon' => 'bi-stack', 'title' => 'Ingredientes', 'desc' => 'Stock base y materias primas.'],
                            ['route' => 'categorias-ingredientes.index', 'icon' => 'bi-tags', 'title' => 'Categorías', 'desc' => 'Organización y clasificación.'],
                            ['route' => 'recetas.index', 'icon' => 'bi-book', 'title' => 'Recetario', 'desc' => 'Fórmulas y costos de producción.'],
                            ['route' => 'produccion.index', 'icon' => 'bi-gear-wide-connected', 'title' => 'Fabricación', 'desc' => 'Órdenes y consumo de stock.'],
                            ['route' => 'proveedores.index', 'icon' => 'bi-truck', 'title' => 'Proveedores', 'desc' => 'Directorio y contactos.'],
                            ['route' => 'pedidoproveedores.index', 'icon' => 'bi-receipt', 'title' => 'Pedidos Compra', 'desc' => 'Órdenes de reposición.'],
                            ['route' => 'ingredientes.inventario', 'icon' => 'bi-house-door', 'title' => 'Almacén', 'desc' => 'Control físico de stock.'],
                        ];
                    @endphp

                    @foreach($modulos as $mod)
                    <div class="col">
                        <div class="card glass-card inventory-card border-0 rounded-4 p-3">
                            <div class="card-body d-flex flex-column">
                                <i class="bi {{ $mod['icon'] }} inventory-icon"></i>
                                <h5 class="fw-bold">{{ $mod['title'] }}</h5>
                                <p class="text-muted small">{{ $mod['desc'] }}</p>
                                
                                <a href="{{ route($mod['route']) }}" class="btn" style="background: var(--panaderia-marron-principal); color: white;">
                                    Acceder <i class="bi bi-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach

                </div>
            </div>
        </main>
    </div>
</div>
@endsection