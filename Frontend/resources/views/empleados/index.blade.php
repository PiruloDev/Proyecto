@extends('layouts.app')

@section('title', 'Gestión de Empleados - Panadería')

@push('styles')
<link href="{{ asset('css/variables.css') }}" rel="stylesheet">
<link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet">
<link href="{{ asset('css/stylempleado.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Component -->
        @include('components.admin-sidebar')

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
            <div class="content-wrapper">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h3">Gestión de Empleados</h2>
                    <a href="{{ route('empleados.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Nuevo Empleado
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($error)
                    <div class="alert alert-warning" role="alert">
                        <i class="bi bi-exclamation-triangle"></i> {{ $error }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($empleados as $empleado)
                                <tr>
                                    <td>{{ $empleado['nombre'] ?? 'N/A' }}</td>
                                    <td>{{ $empleado['email'] ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $empleado['rol'] ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        @if(isset($empleado['id']) && $empleado['id'])
                                            <div class="btn-actions">
                                                {{-- Botón Editar (PATCH) --}}
                                                <a href="{{ route('empleados.edit', $empleado['id']) }}"
                                                   class="btn btn-success btn-sm"
                                                   title="Editar empleado (ID: {{ $empleado['id'] }})">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                {{-- Botón Eliminar (DELETE) --}}
                                                <form action="{{ route('empleados.destroy', $empleado['id']) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirmarEliminacion({{ json_encode($empleado['nombre'] ?? 'este empleado') }}, {{ $empleado['id'] }})">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-danger btn-sm"
                                                            title="Eliminar empleado (ID: {{ $empleado['id'] }})">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-muted small">Sin acciones disponibles (ID no válido)</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="bi bi-inbox fs-3 d-block mb-2 text-muted"></i>
                                        <span class="text-muted">No hay empleados registrados</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

@push('scripts')
<script>
    function confirmarEliminacion(nombreEmpleado, empleadoId) {
        console.log('=== DEBUG ELIMINAR ===');
        console.log('Nombre:', nombreEmpleado);
        console.log('ID:', empleadoId, 'Tipo:', typeof empleadoId);

        return confirm('¿Estás seguro de que deseas eliminar al empleado "' + nombreEmpleado + '" (ID: ' + empleadoId + ')?\n\nEsta acción no se puede deshacer.');
    }
</script>
@endpush
@endsection
