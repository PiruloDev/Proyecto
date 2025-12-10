{{-- resources/views/inventarioviews/ingredientes/index.blade.php (Refactorizado) --}}

@extends('layouts.app') 

@section('title', 'Gestión de Ingredientes - El Castillo del Pan')

@push('styles')
    {{-- Estilos del dashboard (asumiendo que variables.css y dashboard-admin.css están disponibles) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    {{-- Puedes mantener tus estilos específicos si son necesarios, pero se recomienda centralizar: --}}
    {{-- <link rel="stylesheet" href="/pre-produccion/PHP Modulos/css/stylemoduloinv.css"> --}} 
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        
        {{-- Side Bar: Incluye el sidebar del nuevo layout --}}
        @include('components.admin-sidebar') 
        
        {{-- CONTENIDO PRINCIPAL --}}
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
            
            {{-- Título y Breadcrumb (como se ve en el dashboard) --}}
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2">Gestión de Ingredientes</h1>
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
                    <i class="fas fa-plus"></i> Agregar Ingrediente
                </button>
                <a href="{{ route('ingredientes.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-sync"></i> Recargar Listado
                </a>
            </div>
                    
            <section id="listado" class="mb-5">
                <h3 class="mb-3 text-secondary">Listado de Ingredientes</h3>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle shadow-sm rounded-3">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Proveedor (ID)</th>
                                <th>Categoría (ID)</th>
                                <th>Nombre</th>
                                <th>Referencia</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($ingredientes as $ing)
                                <tr>
                                    {{-- DATOS DEL DTO --}}
                                    <td>{{ $ing['idIngrediente'] }}</td>
                                    <td>{{ $ing['idProveedor'] }}</td>
                                    <td>{{ $ing['idCategoria'] }}</td>
                                    <td>{{ $ing['nombreIngrediente'] }}</td>
                                    <td>{{ $ing['referenciaIngrediente'] }}</td>

                                    <td class="text-nowrap">
                                        {{-- BOTÓN EDITAR (Modal) --}}
                                        <button class="btn btn-warning btn-sm me-1" data-bs-toggle="modal"
                                            data-bs-target="#editModal-{{ $ing['idIngrediente'] }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        {{-- BOTÓN ELIMINAR --}}
                                        <form method="POST"
                                            action="{{ route('ingredientes.destroy', $ing['idIngrediente']) }}"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('¿Eliminar este ingrediente?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- ================== MODAL EDITAR (Mantenido) ================== --}}
                                <div class="modal fade" id="editModal-{{ $ing['idIngrediente'] }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">

                                            <div class="modal-header bg-warning text-white">
                                                <h5 class="modal-title">Editar Ingrediente #{{ $ing['idIngrediente'] }}</h5>
                                                <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <form method="POST"
                                                action="{{ route('ingredientes.update', $ing['idIngrediente']) }}"
                                                class="row g-3 p-4">
                                                @csrf
                                                @method('PUT')

                                                <div class="col-md-6">
                                                    <label class="form-label">Nombre</label>
                                                    <input type="text" name="nombreIngrediente"
                                                        class="form-control"
                                                        value="{{ $ing['nombreIngrediente'] }}" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Referencia</label>
                                                    <input type="text" name="referenciaIngrediente"
                                                        class="form-control"
                                                        value="{{ $ing['referenciaIngrediente'] }}" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Proveedor (ID)</label>
                                                    <input type="number" name="idProveedor" class="form-control"
                                                        value="{{ $ing['idProveedor'] }}" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Categoría (ID)</label>
                                                    <input type="number" name="idCategoria" class="form-control"
                                                        value="{{ $ing['idCategoria'] }}" required>
                                                </div>

                                                <div class="col-12 mt-4">
                                                    <button type="submit" class="btn btn-warning w-100">
                                                        <i class="fas fa-save"></i> Guardar Cambios
                                                    </button>
                                                </div>

                                            </form>

                                        </div>
                                    </div>
                                </div>

                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No hay ingredientes registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- MODAL CREAR INGREDIENTE (Mantenido) --}}
            <section class="mb-5">
                <div class="modal fade" id="crearModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">

                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">Agregar Nuevo Ingrediente</h5>
                                <button class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <form method="POST" action="{{ route('ingredientes.store') }}" class="row g-3 p-4">
                                @csrf

                                <div class="col-md-6">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="nombreIngrediente" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Referencia</label>
                                    <input type="text" name="referenciaIngrediente" class="form-control"
                                        required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Proveedor (ID)</label>
                                    <input type="number" name="idProveedor" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Categoría (ID)</label>
                                    <input type="number" name="idCategoria" class="form-control" required>
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-plus-circle"></i> Guardar Ingrediente
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