@extends('layouts.app')

@section('title', 'Gestión de Categorías')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/stylemoduloinv.css') }}">
@endpush

@section('content')

<div class="container-fluid">
    <div class="row g-0">

        {{-- Sidebar --}}
        @include('partials.sidebar-inventario')

        <div class="col-md-9 col-lg-10 main-content">

            <h1 class="mb-3">Gestión de Categorías</h1>
            <p class="lead">Administra las categorías de ingredientes registradas en el sistema.</p>

            {{-- ALERTAS --}}
            @if (session('status'))
                <div class="alert alert-{{ session('status_type') }} mt-3">
                    {{ session('status') }}
                </div>
            @endif

            {{-- BOTÓN CREAR --}}
            <button class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#modalCrear">
                <i class="fas fa-plus"></i> Nueva Categoría
            </button>

            {{-- TABLA --}}
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre Categoría</th>
                            <th style="width: 180px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categorias as $c)
                        <tr>
                            <td>{{ $c['idCategoriaIngrediente'] }}</td>
                            <td>{{ $c['nombreCategoria'] }}</td>

                            <td>
                                {{-- Botón Editar --}}
                                <button
                                    class="btn btn-warning btn-sm btnEditar"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditar"
                                    data-id="{{ $c['idCategoriaIngrediente'] }}"
                                    data-nombre="{{ $c['nombreCategoria'] }}"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>

                                {{-- Formulario Eliminar --}}
                                <form action="{{ route('categorias.destroy') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="idCategoria" value="{{ $c['idCategoriaIngrediente'] }}">
                                    <button class="btn btn-danger btn-sm"
                                        onclick="return confirm('¿Eliminar esta categoría?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>

                        </tr>

                        @empty
                        <tr>
                            <td colspan="3" class="text-center">No hay categorías registradas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


            {{-- ========================================================= --}}
            {{--   MODAL CREAR CATEGORÍA                                   --}}
            {{-- ========================================================= --}}
            <div class="modal fade" id="modalCrear" tabindex="-1">
                <div class="modal-dialog">
                    <form class="modal-content" method="POST" action="{{ route('categorias.store') }}">
                        @csrf
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Nueva Categoría</h5>
                            <button class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">

                            <label class="form-label">Nombre de la Categoría:</label>
                            <input type="text" name="nombreCategoria" class="form-control" required>

                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ========================================================= --}}
            {{--   MODAL EDITAR CATEGORÍA                                  --}}
            {{-- ========================================================= --}}
            <div class="modal fade" id="modalEditar" tabindex="-1">
                <div class="modal-dialog">
                    <form class="modal-content" method="POST" action="{{ route('categorias.update') }}">
                        @csrf
                        
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title">Editar Categoría</h5>
                            <button class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <input type="hidden" name="idCategoria" id="editId">

                            <label class="form-label">Nombre de la Categoría:</label>
                            <input type="text" name="nombreCategoria" id="editNombre" class="form-control" required>

                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button class="btn btn-warning">Actualizar</button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Script para cargar datos en el modal de editar --}}
<script>
document.addEventListener("DOMContentLoaded", () => {
    const modalEditar = document.getElementById("modalEditar");

    modalEditar.addEventListener("show.bs.modal", function (event) {
        const button = event.relatedTarget;

        const id = button.getAttribute("data-id");
        const nombre = button.getAttribute("data-nombre");

        document.getElementById("editId").value = id;
        document.getElementById("editNombre").value = nombre;
    });
});
</script>

@endsection
