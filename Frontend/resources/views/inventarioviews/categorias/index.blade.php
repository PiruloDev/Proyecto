{{-- resources/views/inventarioviews/categoriasIngredientes/index.blade.php (Refactorizado) --}}

@extends('layouts.app') 

@section('title', 'Gestión de Categorías de Ingredientes - El Castillo del Pan')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    {{-- <link rel="stylesheet" href="/pre-produccion/PHP Modulos/css/stylemoduloinv.css"> --}}
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        
        @include('components.admin-sidebar') 
        
        {{-- CONTENIDO PRINCIPAL --}}
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
            
            {{-- Título y Volver al Módulo Inventario --}}
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2">Gestión de Categorías de Ingredientes</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    
                </div>
            </div>

            {{-- MENSAJES --}}
            @if (session('success'))
                <div class="alert alert-success my-3">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger my-3">{{ session('error') }}</div>
            @endif

            {{-- Botón y Acciones --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fas fa-plus"></i> Agregar Categoría
                </button>
                <a href="{{ route('categorias-ingredientes.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-sync"></i> Recargar Listado
                </a>
            </div>
                    
            <section id="listado" class="mb-5">
                <h3 class="mb-3 text-secondary">Listado de Categorías</h3>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle shadow-sm rounded-3">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre Categoría</th>
                                <th style="width: 150px;">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($categorias as $cat)
                                <tr>
                                    <td>{{ $cat['idCategoriaIngrediente'] }}</td>
                                    <td>{{ $cat['nombreCategoria'] }}</td>

                                    <td class="text-nowrap">
                                        {{-- BOTÓN EDITAR (Modal) --}}
                                        <button class="btn btn-warning btn-sm me-1" data-bs-toggle="modal"
                                            data-bs-target="#editModal-{{ $cat['idCategoriaIngrediente'] }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        {{-- BOTÓN ELIMINAR --}}
                                        <form method="POST"
                                            action="{{ route('categorias-ingredientes.destroy', $cat['idCategoriaIngrediente']) }}"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('¿Eliminar esta categoría?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- ================== MODAL EDITAR ================== --}}
                                <div class="modal fade" id="editModal-{{ $cat['idCategoriaIngrediente'] }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">

                                            <div class="modal-header bg-warning text-white">
                                                <h5 class="modal-title">Editar Categoría #{{ $cat['idCategoriaIngrediente'] }}</h5>
                                                <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <form method="POST"
                                                action="{{ route('categorias-ingredientes.update', $cat['idCategoriaIngrediente']) }}"
                                                class="p-4">
                                                @csrf
                                                @method('PUT')

                                                <div class="mb-3">
                                                    <label class="form-label">Nombre de la Categoría</label>
                                                    <input type="text" name="nombreCategoria"
                                                        class="form-control"
                                                        value="{{ $cat['nombreCategoria'] }}" required>
                                                </div>

                                                <div class="d-grid mt-4">
                                                    <button type="submit" class="btn btn-warning">
                                                        <i class="fas fa-save"></i> Guardar Cambios
                                                    </button>
                                                </div>

                                            </form>

                                        </div>
                                    </div>
                                </div>

                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No hay categorías registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- MODAL CREAR CATEGORÍA --}}
            <section class="mb-5">
                <div class="modal fade" id="crearModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">Agregar Nueva Categoría</h5>
                                <button class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <form method="POST" action="{{ route('categorias-ingredientes.store') }}" class="p-4">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Nombre de la Categoría</label>
                                    <input type="text" name="nombreCategoria" class="form-control" 
                                        placeholder="Ej: Lácteos, Harinas, Frutas" required>
                                </div>

                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-plus-circle"></i> Guardar
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </section>

        </main>

    </div>
</div>
@endsection