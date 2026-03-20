@extends('layouts.app')

@section('title', 'Editar Empleado - Panadería')

@push('styles')
<link href="{{ asset('css/variables.css') }}" rel="stylesheet">
<link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet">
<style>
    .form-container {
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(10px);
        border-radius: var(--panaderia-radius-lg);
        padding: 2rem;
        box-shadow: var(--panaderia-shadow-md);
        max-width: 600px;
        margin: 0 auto;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <main class="col-12 main-content">
            <div class="mb-4">
                <a href="{{ route('empleados.index') }}" class="btn btn-panaderia-action">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>

            <div class="form-container">
                <h2 class="h3 mb-4">Editar Empleado</h2>

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('empleados.update', $empleado['id']) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre Completo *</label>
                        <input type="text"
                               class="form-control @error('nombre') is-invalid @enderror"
                               id="nombre"
                               name="nombre"
                               value="{{ old('nombre', $empleado['nombre']) }}"
                               required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               id="email"
                               name="email"
                               value="{{ old('email', $empleado['email']) }}"
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="contrasena" class="form-label">Nueva Contraseña</label>
                        <input type="password"
                               class="form-control @error('contrasena') is-invalid @enderror"
                               id="contrasena"
                               name="contrasena">
                        <small class="form-text text-muted">Deja en blanco si no deseas cambiarla. Mínimo 6 caracteres.</small>
                        @error('contrasena')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Actualizar Empleado
                        </button>
                        <a href="{{ route('empleados.index') }}" class="btn btn-secondary">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
@endsection
