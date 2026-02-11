{{-- resources/views/dashboard/inventario.blade.php --}}

@extends('layouts.app') 

@section('title', 'Dashboard - Módulo Inventario y Producción')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="{{ asset('css/variables.css') }}" rel="stylesheet">
<link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    /* Asegura que todas las cards tengan la misma altura exacta para que las filas no se rompan */
    .compact-module-card {
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        flex-direction: column;
        height: 100%; /* Importante para el encuadre */
        min-height: 200px; 
    }
    
    .compact-module-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1) !important;
    }

    /* Ajuste de iconos para que no se desborden en pantallas pequeñas */
    .compact-module-card .card-icon {
        font-size: 2.2rem;
    }

    /* Forzar que el pie de la card (el botón) siempre esté al fondo */
    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    /* Padding superior para que el contenido no choque con el botón de hamburguesa en móvil */
    @media (max-width: 768px) {
        main {
            padding-top: 60px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        
        {{-- Side Bar: Se mantiene intacto según tu requerimiento --}}
        @include('components.admin-sidebar') 
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
            
            {{-- Título de la Sección --}}
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2"> Módulo de Inventario</h1>
            </div>

            <h3 class="mb-3 mt-4 text-primary">Accesos Directos</h3>
            
            {{-- SECCIÓN: Inventario y Catálogos --}}
            {{-- row-cols-1 (móvil), row-cols-sm-2 (tablet), row-cols-lg-4 (escritorio) --}}
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4 mb-4">
                
                {{-- Card 1: Inventario de Ingredientes (Ruta original 1) --}}
                <div class="col">
                    <div class="card glass-card border-0 rounded-4 shadow-sm compact-module-card">
                        <a href="{{ route('ingredientes.index') }}" class="text-decoration-none text-dark h-100">
                            <div class="card-body text-center p-3">
                                <div>
                                    <i class="fas fa-boxes-stacked card-icon mb-2" style="color: var(--panaderia-marron-principal);"></i>
                                    <h5 class="mt-2">Inventario</h5>
                                    <p class="text-muted small">Stock de Ingredientes.</p>
                                </div>
                                <div class="d-flex justify-content-center mt-auto">
                                    <span class="btn btn-sm btn-outline-dark w-100">Ver Stock</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                {{-- Card 2: Categorías de Ingredientes --}}
                <div class="col">
                    <div class="card glass-card border-0 rounded-4 shadow-sm compact-module-card">
                        <a href="{{ route('categorias-ingredientes.index') }}" class="text-decoration-none text-dark h-100">
                            <div class="card-body text-center p-3">
                                <div>
                                    <i class="bi bi-tag card-icon mb-2" style="color: var(--panaderia-marron-principal);"></i>
                                    <h5 class="mt-2">Clasificación</h5>
                                    <p class="text-muted small">Organización de Ingredientes.</p>
                                </div>
                                <div class="d-flex justify-content-center mt-auto">
                                    <span class="btn btn-sm btn-outline-dark w-100">Gestionar</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                
                {{-- Card 3: Recetario --}}
                <div class="col">
                    <div class="card glass-card border-0 rounded-4 shadow-sm compact-module-card">
                        <a href="{{ route('recetas.index') }}" class="text-decoration-none text-dark h-100">
                            <div class="card-body text-center p-3">
                                <div>
                                    <i class="fas fa-book-open card-icon mb-2" style="color: var(--panaderia-marron-principal);"></i>
                                    <h5 class="mt-2">Recetario</h5>
                                    <p class="text-muted small">Definición de productos (BOM).</p>
                                </div>
                                <div class="d-flex justify-content-center mt-auto">
                                    <span class="btn btn-sm btn-outline-dark w-100">Gestionar</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                {{-- Card 4: Producción --}}
                <div class="col">
                    <div class="card glass-card border-0 rounded-4 shadow-sm compact-module-card">
                        <a href="{{ route('produccion.index') }}" class="text-decoration-none text-dark h-100">
                            <div class="card-body text-center p-3">
                                <div>
                                    <i class="fas fa-cookie-cutter card-icon mb-2" style="color: var(--panaderia-marron-principal);"></i>
                                    <h5 class="mt-2">Fabricación</h5>
                                    <p class="text-muted small">Órdenes de Producción.</p>
                                </div>
                                <div class="d-flex justify-content-center mt-auto">
                                    <span class="btn btn-sm btn-outline-dark w-100">Ver Órdenes</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                {{-- Card 5: Proveedores --}}
                <div class="col">
                    <div class="card glass-card border-0 rounded-4 shadow-sm compact-module-card">
                        <a href="{{ route('proveedores.index') }}" class="text-decoration-none text-dark h-100">
                            <div class="card-body text-center p-3">
                                <div>
                                    <i class="fas fa-truck-moving card-icon mb-2" style="color: var(--panaderia-marron-principal);"></i>
                                    <h5 class="mt-2">Proveedores</h5>
                                    <p class="text-muted small">Directorio de Compras.</p>
                                </div>
                                <div class="d-flex justify-content-center mt-auto">
                                    <span class="btn btn-sm btn-outline-dark w-100">Ver Listado</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                
                {{-- Card 6: Pedidos a Proveedores --}}
                <div class="col">
                    <div class="card glass-card border-0 rounded-4 shadow-sm compact-module-card">
                        <a href="{{ route('pedidoproveedores.index') }}" class="text-decoration-none text-dark h-100">
                            <div class="card-body text-center p-3">
                                <div>
                                    <i class="fas fa-receipt card-icon mb-2" style="color: var(--panaderia-marron-principal);"></i>
                                    <h5 class="mt-2">Pedidos de Compra</h5>
                                    <p class="text-muted small">Seguimiento de Órdenes.</p>
                                </div>
                                <div class="d-flex justify-content-center mt-auto">
                                    <span class="btn btn-sm btn-outline-dark w-100">Ver Órdenes</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                {{-- Card 7: Inventario Alternativo (Ruta original 2) --}}
                <div class="col">
                    <div class="card glass-card border-0 rounded-4 shadow-sm compact-module-card">
                        <a href="{{ route('ingredientes.inventario') }}" class="text-decoration-none text-dark h-100">
                            <div class="card-body text-center p-3">
                                <div>
                                    <i class="fas fa-warehouse card-icon mb-2" style="color: var(--panaderia-marron-principal);"></i>
                                    <h5 class="mt-2">Almacén Central</h5>
                                    <p class="text-muted small">Gestión Física de Stock.</p>
                                </div>
                                <div class="d-flex justify-content-center mt-auto">
                                    <span class="btn btn-sm btn-outline-dark w-100">Control Físico</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

            </div> {{-- Fin de la rejilla de tarjetas --}}

        </main>
    </div>
</div>
@endsection