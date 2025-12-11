{{-- resources/views/inventarioviews/proveedores/index.blade.php (Refactorizado) --}}

@extends('layouts.app') 

@section('title', 'Gestión de Proveedores - El Castillo del Pan')

@push('styles')
    {{-- Estilos necesarios para la integración con el layout de dashboard --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    {{-- Si el estilo 'stylemoduloinv.css' es crucial y contiene estilos específicos, inclúyelo: --}}
    {{-- <link rel="stylesheet" href="/pre-produccion/PHP Modulos/css/stylemoduloinv.css"> --}}
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        
        {{-- Side Bar: Incluye el sidebar del nuevo layout --}}
        @include('components.admin-sidebar') 
        
        {{-- CONTENIDO PRINCIPAL --}}
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
            
            {{-- Título y Volver al Módulo Inventario --}}
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2">Gestión de Proveedores</h1>
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
            
            {{-- Botones de Acción --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearModal">
                    <i class="fas fa-plus"></i> Agregar Proveedor
                </button>
                <a href="{{ route('proveedores.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-sync"></i> Recargar Listado
                </a>
            </div>
                    
            <section id="listado" class="mb-5">
                <h3 class="mb-3 text-secondary">Listado de Proveedores</h3>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle shadow-sm rounded-3">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                                <th>Dirección</th>
                                <th>Estado</th>
                                <th style="width: 120px;">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($proveedores as $prov)
                                <tr>
                                    <td>{{ $prov['idProveedor'] }}</td>
                                    <td>{{ $prov['nombreProv'] }}</td>
                                    <td>{{ $prov['telefonoProv'] }}</td>
                                    <td>{{ $prov['emailProv'] }}</td>
                                    <td>{{ $prov['direccionProv'] ?? 'N/A' }}</td>
                                    <td>
                                        @if($prov['activoProv'])
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </td>

                                    <td class="text-nowrap">
                                        {{-- BOTÓN EDITAR (Modal) --}}
                                        <button class="btn btn-warning btn-sm me-1" data-bs-toggle="modal"
                                            data-bs-target="#editModal-{{ $prov['idProveedor'] }}" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        {{-- BOTÓN ELIMINAR --}}
                                        <form method="POST"
                                            action="{{ route('proveedores.destroy', $prov['idProveedor']) }}"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('¿Eliminar este proveedor?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- ================== MODAL EDITAR para cada proveedor ================== --}}
                                <div class="modal fade" id="editModal-{{ $prov['idProveedor'] }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">

                                            <div class="modal-header bg-warning text-dark">
                                                <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Proveedor: {{ $prov['nombreProv'] }}</h5>
                                                <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <form method="POST"
                                                action="{{ route('proveedores.update', $prov['idProveedor']) }}"
                                                class="row g-3 p-4">
                                                @csrf
                                                @method('PUT')

                                                <div class="col-md-6">
                                                    <label class="form-label">Nombre del Proveedor</label>
                                                    <input type="text" name="nombreProv"
                                                        class="form-control"
                                                        value="{{ $prov['nombreProv'] }}" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Teléfono</label>
                                                    <input type="text" name="telefonoProv"
                                                        class="form-control"
                                                        value="{{ $prov['telefonoProv'] }}" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" name="emailProv" class="form-control"
                                                        value="{{ $prov['emailProv'] }}" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Estado</label>
                                                    <select name="activoProv" class="form-select" required>
                                                        <option value="1" {{ $prov['activoProv'] ? 'selected' : '' }}>Activo</option>
                                                        <option value="0" {{ !$prov['activoProv'] ? 'selected' : '' }}>Inactivo</option>
                                                    </select>
                                                </div>

                                                <div class="col-12">
                                                    <label class="form-label">Dirección</label>
                                                    <textarea name="direccionProv" class="form-control" rows="2" required>{{ $prov['direccionProv'] }}</textarea>
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
                                    <td colspan="7" class="text-center">No hay proveedores registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- MODAL CREAR PROVEEDOR (Único) --}}
            <div class="modal fade" id="crearModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title"><i class="fas fa-plus"></i> Agregar Nuevo Proveedor</h5>
                            <button class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <form method="POST" action="{{ route('proveedores.store') }}" class="row g-3 p-4">
                            @csrf

                            <div class="col-md-6">
                                <label class="form-label">Nombre del Proveedor</label>
                                <input type="text" name="nombreProv" class="form-control" 
                                            placeholder="Ej: Distribuidora XYZ" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefonoProv" class="form-control"
                                            placeholder="Ej: +57 300 123 4567" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="emailProv" class="form-control"
                                            placeholder="proveedor@ejemplo.com" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Estado</label>
                                <select name="activoProv" class="form-select" required>
                                    <option value="1" selected>Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Dirección (Opcional)</label>
                                <textarea name="direccionProv" class="form-control" rows="2" 
                                            placeholder="Dirección completa del proveedor"></textarea>
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save"></i> Guardar Proveedor
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>
@endsection

@push('scripts')
    {{-- Scripts adicionales si los hubiera, en este caso, solo necesitas el bundle de Bootstrap del layout principal --}}
@endpush