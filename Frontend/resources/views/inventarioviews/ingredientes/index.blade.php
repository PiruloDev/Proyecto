@extends('layouts.app') 

@section('title', 'Gestión de Ingredientes - Inventario')

@push('styles')
    {{-- Asegúrate de que este CSS esté accesible en la carpeta public/css/ --}}
    <link rel="stylesheet" href="{{ asset('css/stylemoduloinv.css') }}">
@endpush

@section('content')

<div class="container-fluid">
  <div class="row g-0">
    
    @include('partials.sidebar-inventario')

    <div class="col-md-9 col-lg-10 main-content">
        
        <header class="mb-5">
            <h1 class="display-5">Gestión de Ingredientes</h1>
            <p class="lead text-secondary">Controla el stock, añade nuevos insumos y gestiona los datos de inventario.</p>
        </header>

        {{-- Mostrar Mensajes Flash (status) --}}
        @if (session('status'))
            <div class="alert alert-{{ session('status_type', 'success') }} alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <section id="agregar" class="mb-5 p-4 border rounded shadow-sm">
          <h2>Agregar Nuevo Ingrediente</h2>
          <form method="POST" action="{{ route('ingredientes.store') }}" class="row g-3">
            @csrf {{-- Token de seguridad de Laravel --}}
            
            <div class="col-md-4">
              <label class="form-label">ID Proveedor</label>
              <input type="number" name="idProveedor" class="form-control" value="{{ old('idProveedor') }}" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">ID Categoría</label>
              <input type="number" name="idCategoria" class="form-control" value="{{ old('idCategoria') }}" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Cantidad</label>
              <input type="number" name="cantidadIngrediente" class="form-control" value="{{ old('cantidadIngrediente') }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Nombre</label>
              <input type="text" name="nombreIngrediente" class="form-control" value="{{ old('nombreIngrediente') }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Referencia</label>
              <input type="text" name="referenciaIngrediente" class="form-control" value="{{ old('referenciaIngrediente') }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Fecha Vencimiento</label>
              <input type="date" name="fechaVencimiento" class="form-control" value="{{ old('fechaVencimiento') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Fecha Entrega</label>
              <input type="date" name="fechaEntregaIngrediente" class="form-control" value="{{ old('fechaEntregaIngrediente') }}">
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="col-12">
              <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Agregar Ingrediente</button>
            </div>
          </form>
        </section>

        <section id="listado" class="mb-5">
          <h2>Listado de Ingredientes</h2>
          
          @if (!is_array($ingredientes))
              <div class="alert alert-danger">
                  <strong>Error de Conexión:</strong> No se pudo obtener el listado de ingredientes. Por favor, asegúrate de que el microservicio (Spring Boot) esté corriendo y que `API_SPRING_URL` en tu `.env` sea correcto.
              </div>
          @else
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Nombre</th>
                      <th>Cantidad</th>
                      <th>Referencia</th>
                      <th>Vencimiento</th>
                      <th>Proveedor ID</th>
                      <th>Categoría ID</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($ingredientes as $ingrediente)
                        <tr>
                            {{-- CORRECCIÓN 1: Usar idIngrediente --}}
                            <td>{{ $ingrediente['idIngrediente'] ?? 'N/A' }}</td>
                            <td>{{ $ingrediente['nombreIngrediente'] ?? '' }}</td>
                            <td>{{ $ingrediente['cantidadIngrediente'] ?? 0 }}</td>
                            <td>{{ $ingrediente['referenciaIngrediente'] ?? '' }}</td>
                            <td>{{ $ingrediente['fechaVencimiento'] ?? 'N/A' }}</td>
                            <td>{{ $ingrediente['idProveedor'] ?? 'N/A' }}</td>
                            <td>{{ $ingrediente['idCategoria'] ?? 'N/A' }}</td>
                            <td>
                                {{-- CORRECCIÓN 2: Usar idIngrediente en el modal target --}}
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal-{{ $ingrediente['idIngrediente'] }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('ingredientes.destroy') }}" method="POST" style="display:inline;">
                                    @csrf
                                    {{-- CORRECCIÓN 3: Usar idIngrediente en el input hidden --}}
                                    <input type="hidden" name="id" value="{{ $ingrediente['idIngrediente'] }}">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar este ingrediente?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No hay ingredientes registrados en el sistema.</td>
                        </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
          @endif
        </section>

        <section id="actualizarCantidad" class="mb-5 p-4 border rounded shadow-sm">
          <h2>Actualizar Cantidad (Rápido)</h2>
          <form method="POST" action="{{ route('ingredientes.updateCantidad') }}" class="row g-3">
            @csrf
            <div class="col-md-6">
              <label class="form-label">ID Ingrediente</label>
              <input type="number" name="id" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Nueva Cantidad</label>
              <input type="number" name="cantidadIngrediente" class="form-control" required>
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-info text-white"><i class="fas fa-sync"></i> Actualizar Cantidad</button>
            </div>
          </form>
        </section>

    </div>
  </div>
</div>

@endsection

@push('scripts')
    {{-- Scripts adicionales si los necesitas --}}
@endpush