@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row">

        {{-- SIDEBAR --}}
        @include('partials.sidebar-inventario')

        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4">

            <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
                <h2 class="fw-bold">Gestión de Proveedores</h2>

                <!-- Botón para abrir modal de crear -->
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrear">
                    <i class="fas fa-plus"></i> Agregar Proveedor
                </button>
            </div>

            {{-- MENSAJES --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- TABLA DE PROVEEDORES --}}
            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                                <th>Estado</th>
                                <th width="150">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($proveedores as $p)
                            <tr>
                                <td>{{ $p['idProveedor'] }}</td>
                                <td>{{ $p['nombreProv'] }}</td>
                                <td>{{ $p['telefonoProv'] ?? '—' }}</td>
                                <td>{{ $p['emailProv'] ?? '—' }}</td>

                                <td>
                                    @if($p['activoProv'])
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-secondary">Inactivo</span>
                                    @endif
                                </td>

                                <td>
                                    {{-- BOTÓN EDITAR --}}
                                    <button class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditar"
                                        data-id="{{ $p['idProveedor'] }}"
                                        data-nombre="{{ $p['nombreProv'] }}"
                                        data-telefono="{{ $p['telefonoProv'] }}"
                                        data-email="{{ $p['emailProv'] }}"
                                        data-activo="{{ $p['activoProv'] }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    {{-- BOTÓN ELIMINAR --}}
                                    <button class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEliminar"
                                        data-id="{{ $p['idProveedor'] }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

{{-- ======================================================
                        MODAL CREAR
====================================================== --}}
<div class="modal fade" id="modalCrear" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" action="{{ route('proveedores.store') }}" method="POST">
            @csrf

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Agregar Proveedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombreProv" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefonoProv" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="emailProv" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Activo</label>
                    <select name="activoProv" class="form-control">
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary">Guardar</button>
            </div>

        </form>
    </div>
</div>

{{-- ======================================================
                        MODAL EDITAR
====================================================== --}}
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="formEditar" method="POST">
            @csrf
            @method('PUT')

            <input type="hidden" name="idProveedor" id="edit-id">

            <div class="modal-header bg-warning">
                <h5 class="modal-title">Editar Proveedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombreProv" id="edit-nombre" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefonoProv" id="edit-telefono" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="emailProv" id="edit-email" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Activo</label>
                    <select name="activoProv" id="edit-activo" class="form-control">
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-warning">Actualizar</button>
            </div>

        </form>
    </div>
</div>

{{-- ======================================================
                        MODAL ELIMINAR
====================================================== --}}
<div class="modal fade" id="modalEliminar" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="formEliminar" method="POST">
            @csrf
            @method('DELETE')

            <input type="hidden" name="idProveedor" id="delete-id">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Eliminar Proveedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                ¿Está seguro que desea eliminar este proveedor?
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger">Eliminar</button>
            </div>

        </form>
    </div>
</div>

@endsection


{{-- ======================================================
                SCRIPT PARA EDITAR Y ELIMINAR
====================================================== --}}
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ============================
         MODAL EDITAR
    ============================ */
    const modalEditar = document.getElementById('modalEditar');

    modalEditar.addEventListener('show.bs.modal', function (event) {

        const button = event.relatedTarget;

        const id = button.getAttribute('data-id');
        const nombre = button.getAttribute('data-nombre');
        const telefono = button.getAttribute('data-telefono');
        const email = button.getAttribute('data-email');
        const activo = button.getAttribute('data-activo');

        document.getElementById('edit-id').value = id;
        document.getElementById('edit-nombre').value = nombre;
        document.getElementById('edit-telefono').value = telefono;
        document.getElementById('edit-email').value = email;
        document.getElementById('edit-activo').value = activo == 1 ? "1" : "0";

        // Ruta dinámica
        const form = document.getElementById('formEditar');
        form.action = `/dashboard/inventario/proveedores/update/${id}`;
    });

    /* ============================
         MODAL ELIMINAR
    ============================ */
    const modalEliminar = document.getElementById('modalEliminar');

    modalEliminar.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');

        document.getElementById('delete-id').value = id;

        const form = document.getElementById('formEliminar');
        form.action = `/dashboard/inventario/proveedores/delete/${id}`;
    });

});
</script>
@endsection
