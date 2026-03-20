@extends('layouts.app')

@section('title', 'Gestión de Clientes - Panadería')

@push('styles')
<link href="{{ asset('css/variables.css') }}" rel="stylesheet">
<link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet">
<style>
    .table-responsive {
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(10px);
        border-radius: var(--panaderia-radius-lg);
        padding: 1.5rem;
        box-shadow: var(--panaderia-shadow-md);
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Component -->
        @include('components.admin-sidebar')

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
            <div class="content-wrapper">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                    <h2 class="dashboard-page-title">Lista de Clientes</h2>
                </div>

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
                                <th>Teléfono</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clientes as $cliente)
                                <tr>
                                    <td>{{ $cliente['nombre'] }}</td>
                                    <td>{{ $cliente['email'] }}</td>
                                    <td>{{ $cliente['telefono'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">
                                        <i class="bi bi-inbox fs-3 d-block mb-2 text-muted"></i>
                                        <span class="text-muted">No hay clientes registrados</span>
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
@endsection
