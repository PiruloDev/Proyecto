{{-- resources/views/dashboard/inventario.blade.php (Versión Compacta) --}}

@extends('layouts.app') 

@section('title', 'Dashboard - Módulo Inventario y Producción')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="{{ asset('css/variables.css') }}" rel="stylesheet">
<link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    /* Estilo específico para las tarjetas de acceso rápido compactas */
    .compact-module-card {
        transition: transform 0.2s, box-shadow 0.2s;
        min-height: 180px; /* Altura mínima para mantener uniformidad */
    }
    .compact-module-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1) !important;
    }
    .compact-module-card .card-icon {
        font-size: 2.5rem; /* Icono más pequeño */
    }
    .compact-module-card h5 {
        font-size: 1rem; /* Título un poco más pequeño */
    }
    .compact-module-card p {
        font-size: 0.75rem; /* Descripción pequeña */
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        
        {{-- Side Bar: Usa el componente que has definido --}}
        @include('components.admin-sidebar') 
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
            
            {{-- Título de la Sección --}}
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2"> Módulo de Inventario</h1>
            </div>

            
            <h3 class="mb-3 mt-4 text-primary">Accesos Directos</h3>
            
            {{-- FILA 1: Inventario y Catálogos --}}
            <div class="row g-4 mb-4">
                
                {{-- Card 1: Inventario de Ingredientes --}}
                <div class="col-6 col-lg-3"> {{-- Cámbiado a col-lg-3 --}}
                    <div class="card glass-card border-0 rounded-4 shadow-sm h-100 compact-module-card">
                        <a href="{{ route('ingredientes.index') }}" class="text-decoration-none text-dark h-100">
                            <div class="card-body text-center d-flex flex-column p-3">
                                <i class="fas fa-boxes-stacked card-icon mb-2" style="color: var(--panaderia-marron-principal);"></i>
                                <h5 class="mt-2">Inventario</h5>
                                <p class="text-muted flex-grow-1 small">Stock de Ingredientes.</p>
                                <div class="d-flex justify-content-center mt-auto">
                                    <span class="btn btn-sm btn-outline-dark">Ver Stock</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                {{-- Card 2: Categorías de Ingredientes --}}
                <div class="col-6 col-lg-3"> {{-- Cámbiado a col-lg-3 --}}
                    <div class="card glass-card border-0 rounded-4 shadow-sm h-100 compact-module-card">
                        <a href="{{ route('categorias-ingredientes.index') }}" class="text-decoration-none text-dark h-100">
                            <div class="card-body text-center d-flex flex-column p-3">
                                <i class="bi bi-tag card-icon mb-2" style="color: var(--panaderia-marron-principal);"></i>
                                <h5 class="mt-2">Clasificación</h5>
                                <p class="text-muted flex-grow-1 small">Organización de Ingredientes.</p>
                                <div class="d-flex justify-content-center mt-auto">
                                    <span class="btn btn-sm btn-outline-dark">Gestionar</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                
                {{-- Card 3: Recetario --}}
                <div class="col-6 col-lg-3"> {{-- Cámbiado a col-lg-3 --}}
                    <div class="card glass-card border-0 rounded-4 shadow-sm h-100 compact-module-card">
                        <a href="{{ route('recetas.index') }}" class="text-decoration-none text-dark h-100">
                            <div class="card-body text-center d-flex flex-column p-3">
                                <i class="fas fa-book-open card-icon mb-2" style="color: var(--panaderia-marron-principal);"></i>
                                <h5 class="mt-2">Recetario</h5>
                                <p class="text-muted flex-grow-1 small">Definición de productos (BOM).</p>
                                <div class="d-flex justify-content-center mt-auto">
                                    <span class="btn btn-sm btn-outline-dark">Gestionar</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                {{-- Card 4: Producción --}}
                <div class="col-6 col-lg-3"> {{-- Cámbiado a col-lg-3 --}}
                    <div class="card glass-card border-0 rounded-4 shadow-sm h-100 compact-module-card">
                        <a href="{{ route('produccion.index') }}" class="text-decoration-none text-dark h-100">
                            <div class="card-body text-center d-flex flex-column p-3">
                                <i class="fas fa-cookie-cutter card-icon mb-2" style="color: var(--panaderia-marron-principal);"></i>
                                <h5 class="mt-2">Fabricación</h5>
                                <p class="text-muted flex-grow-1 small">Órdenes de Producción.</p>
                                <div class="d-flex justify-content-center mt-auto">
                                    <span class="btn btn-sm btn-outline-dark">Ver Órdenes</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            
            {{-- FILA 2: Compras --}}
            <div class="row g-4 mb-5">
                
                {{-- Card 5: Proveedores --}}
                <div class="col-6 col-lg-3"> {{-- Cámbiado a col-lg-3 --}}
                    <div class="card glass-card border-0 rounded-4 shadow-sm h-100 compact-module-card">
                        <a href="{{ route('proveedores.index') }}" class="text-decoration-none text-dark h-100">
                            <div class="card-body text-center d-flex flex-column p-3">
                                <i class="fas fa-truck-moving card-icon mb-2" style="color: var(--panaderia-marron-principal);"></i>
                                <h5 class="mt-2">Proveedores</h5>
                                <p class="text-muted flex-grow-1 small">Directorio de Compras.</p>
                                <div class="d-flex justify-content-center mt-auto">
                                    <span class="btn btn-sm btn-outline-dark">Ver Listado</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                
                {{-- Card 6: Pedidos a Proveedores --}}
                <div class="col-6 col-lg-3"> {{-- Cámbiado a col-lg-3 --}}
                    <div class="card glass-card border-0 rounded-4 shadow-sm h-100 compact-module-card">
                        <a href="{{ route('pedidoproveedores.index') }}" class="text-decoration-none text-dark h-100">
                            <div class="card-body text-center d-flex flex-column p-3">
                                <i class="fas fa-receipt card-icon mb-2" style="color: var(--panaderia-marron-principal);"></i>
                                <h5 class="mt-2">Pedidos de Compra</h5>
                                <p class="text-muted flex-grow-1 small">Seguimiento de Órdenes.</p>
                                <div class="d-flex justify-content-center mt-auto">
                                    <span class="btn btn-sm btn-outline-dark">Ver Órdenes</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                
            </div>
            
            {{-- Card 1: Inventario de Ingredientes --}}
<div class="col-6 col-lg-3">
    <div class="card glass-card border-0 rounded-4 shadow-sm h-100 compact-module-card">
        {{-- Cambiar esta línea: --}}
        <a href="{{ route('ingredientes.inventario') }}" class="text-decoration-none text-dark h-100">
            <div class="card-body text-center d-flex flex-column p-3">
                <i class="fas fa-boxes-stacked card-icon mb-2" style="color: var(--panaderia-marron-principal);"></i>
                <h5 class="mt-2">Inventario</h5>
                <p class="text-muted flex-grow-1 small">Stock de Ingredientes.</p>
                <div class="d-flex justify-content-center mt-auto">
                    <span class="btn btn-sm btn-outline-dark">Ver Stock</span>
                </div>
            </div>
        </a>
    </div>
</div>

            {{-- Aquí puedes agregar otros gráficos o widgets más grandes --}}

        </main>
    </div>
</div>

@endsection