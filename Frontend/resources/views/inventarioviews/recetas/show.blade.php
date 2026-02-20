@extends('layouts.app')

@section('title', 'Detalle de Receta')

@push('styles')
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .recipe-header-neutral {
            background-color: #1a202c; /* Gris muy oscuro, casi negro */
            border-radius: 12px;
            color: white;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .table-custom thead {
            background-color: #f8fafc;
            border-bottom: 2px solid #edf2f7;
        }
        .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,0.5); }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('components.admin-sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
            <div class="recipe-header-neutral mt-4 shadow-sm">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-2">
                                <li class="breadcrumb-item"><a href="{{ route('recetas.index') }}" class="text-white-50">Recetas</a></li>
                                <li class="breadcrumb-item active text-white" aria-current="page">Ficha Técnica</li>
                            </ol>
                        </nav>
                        <h1 class="h2 fw-bold mb-0">{{ $producto->NOMBRE_PRODUCTO ?? 'Referencia #'.$idProducto }}</h1>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn btn-warning fw-bold px-4" data-bs-toggle="modal" data-bs-target="#editarModal">
                            <i class="fas fa-edit me-2"></i>Editar Ficha
                        </button>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold text-secondary">
                        <i class="fas fa-list-ol me-2"></i>Ingredientes y Cantidades
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Componente</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-center">U. Medida</th>
                                    <th class="text-end pe-4">ID Receta</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detalles as $detalle)
                                    <tr>
                                        <td class="ps-4 fw-medium">ID: {{ $detalle['idIngrediente'] }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border px-3 py-2">
                                                {{ number_format($detalle['cantidadRequerida'], 3) }}
                                            </span>
                                        </td>
                                        <td class="text-center text-muted">Unidad {{ $detalle['idUnidad'] }}</td>
                                        <td class="text-end pe-4 text-monospace small">#{{ $detalle['idReceta'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

{{-- Los modales y scripts se mantienen igual, pero asegúrate de que el botón de "Guardar" sea btn-dark o btn-success --}}
@endsection