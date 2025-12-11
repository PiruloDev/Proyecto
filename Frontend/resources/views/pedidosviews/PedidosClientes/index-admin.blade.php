@extends('layouts.app')

@section('title', 'Gestión de Pedidos (Administrador)')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        
        {{-- ================================================= --}}
        {{-- SIDEBAR FIJO DEL ADMINISTRADOR --}}
        {{-- ================================================= --}}
        @include('components.admin-sidebar') 
        
        {{-- CONTENIDO PRINCIPAL --}}
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
            
            {{-- Título y Botones de Acción --}}
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2">Listado de Pedidos (Panel de Administración)</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    {{-- Botón Crear Pedido - Abre el modal (ya no es un enlace de ruta) --}}
                    <button type="button" class="btn btn-primary me-2" id="btn-create-pedido">
                        <i class="fas fa-plus"></i> Crear Pedido
                    </button>
                    {{-- Botón Recargar - USA RUTA DE ADMIN --}}
                    <a href="{{ route('admin.pedidos.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-sync"></i> Recargar Listado
                    </a>
                </div>
            </div>

            {{-- MENSAJES DE SESIÓN --}}
            @if (session('success'))
                <div class="alert alert-success my-3">{{ session('success') }}</div>
            @endif  

            @if (session('error'))
                <div class="alert alert-danger my-3">{{ session('error') }}</div>
            @endif
            
            <section id="listado-pedidos" class="mb-5">
                <h3 class="mb-3 text-secondary">Pedidos Registrados</h3>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle shadow-sm rounded-3">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Cliente ID</th>
                                <th>Empleado ID</th>
                                <th>Estado ID</th>
                                <th>Total</th>
                                <th>Ingreso</th>
                                <th>Entrega</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($pedidos as $pedido)
                                <tr>
                                    <td>{{ $pedido['id_PEDIDO'] }}</td>
                                    <td>{{ $pedido['id_CLIENTE'] }}</td>
                                    <td>{{ $pedido['id_EMPLEADO'] }}</td>
                                    <td>{{ $pedido['id_ESTADO_PEDIDO'] }}</td>
                                    <td>${{ number_format($pedido['total_PRODUCTO'] ?? 0, 2) }}</td>
                                    <td>{{ $pedido['fecha_INGRESO'] ?? 'N/A' }}</td>
                                    <td>{{ $pedido['fecha_ENTREGA'] ?? 'N/A' }}</td>

                                    <td class="text-nowrap">
                                        {{-- Botón Editar - Abre el modal e inyecta datos --}}
                                        <button type="button" 
                                            class="btn btn-warning btn-sm me-1 btn-edit-pedido" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#pedidoModal"
                                            data-pedido="{{ json_encode($pedido) }}">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>

                                        {{-- Formulario Eliminar - USA RUTA DE ADMIN --}}
                                        <form method="POST"
                                            action="{{ route('admin.pedidos.destroy', $pedido['id_PEDIDO']) }}"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('¿Está seguro de eliminar este pedido?')">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No hay pedidos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

        </main>

    </div>
</div>

{{-- ================================================= --}}
{{-- MODAL ÚNICO PARA CREAR Y EDITAR PEDIDO --}}
{{-- ================================================= --}}
<div class="modal fade" id="pedidoModal" tabindex="-1" aria-labelledby="pedidoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pedidoModalLabel">Crear/Editar Pedido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="pedidoForm" method="POST" action="">
                @csrf
                {{-- Campo oculto para el método PUT/PATCH en edición (el script lo cambia) --}}
                <input type="hidden" name="_method" value="POST" id="formMethod"> 
                
                <div class="modal-body row g-3">
                    <input type="hidden" name="id_PEDIDO" id="modal_id_PEDIDO">

                    <div class="col-md-6">
                        <label for="modal_ID_CLIENTE" class="form-label">ID Cliente</label>
                        <input type="number" class="form-control" id="modal_ID_CLIENTE" name="ID_CLIENTE" required>
                    </div>

                    <div class="col-md-6">
                        <label for="modal_ID_EMPLEADO" class="form-label">ID Empleado</label>
                        <input type="number" class="form-control" id="modal_ID_EMPLEADO" name="ID_EMPLEADO" required>
                    </div>

                    <div class="col-md-6">
                        <label for="modal_ID_ESTADO_PEDIDO" class="form-label">ID Estado Pedido</label>
                        <input type="number" class="form-control" id="modal_ID_ESTADO_PEDIDO" name="ID_ESTADO_PEDIDO" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="modal_FECHA_ENTREGA" class="form-label">Fecha de Entrega (Opcional)</label>
                        <input type="date" class="form-control" id="modal_FECHA_ENTREGA" name="FECHA_ENTREGA">
                    </div>

                    <div class="col-md-6">
                        <label for="modal_TOTAL_PRODUCTO" class="form-label">Total Producto</label>
                        <input type="number" step="0.01" class="form-control" id="modal_TOTAL_PRODUCTO" name="TOTAL_PRODUCTO" required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary" id="modalSubmitButton">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- FIN DEL MODAL --}}

@endsection

{{-- ================================================= --}}
{{-- SCRIPTS PARA MANEJAR EL MODAL Y EL FORMULARIO --}}
{{-- ================================================= --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalElement = document.getElementById('pedidoModal');
        const modal = new bootstrap.Modal(modalElement);
        const form = document.getElementById('pedidoForm');
        const modalTitle = document.getElementById('pedidoModalLabel');
        const formMethod = document.getElementById('formMethod');
        const createButton = document.getElementById('btn-create-pedido');

        // Función para preparar el modal para CREAR
        function setupCreate() {
            modalTitle.textContent = 'Crear Nuevo Pedido';
            // Configura la acción para STORE (POST)
            form.action = "{{ route('admin.pedidos.store') }}";
            formMethod.value = 'POST';
            form.reset(); // Limpia los campos del formulario
        }

        // 1. Configurar botón "Crear Pedido"
        createButton.addEventListener('click', function(e) {
            setupCreate();
            modal.show();
        });

        // 2. Configurar botones "Editar" en la tabla
        document.querySelectorAll('.btn-edit-pedido').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                // Obtener datos del pedido desde el atributo data-pedido (JSON)
                const pedidoData = JSON.parse(this.getAttribute('data-pedido'));
                
                modalTitle.textContent = 'Editar Pedido #' + pedidoData.id_PEDIDO;
                
                // Configurar la acción del formulario para UPDATE (PUT)
                // Se construye la URL: /admin/pedidos/{id}
                form.action = "{{ url('admin/pedidos') }}/" + pedidoData.id_PEDIDO;
                formMethod.value = 'PUT'; 

                // Llenar los campos del modal
                document.getElementById('modal_id_PEDIDO').value = pedidoData.id_PEDIDO || '';
                document.getElementById('modal_ID_CLIENTE').value = pedidoData.id_CLIENTE || '';
                document.getElementById('modal_ID_EMPLEADO').value = pedidoData.id_EMPLEADO || '';
                document.getElementById('modal_ID_ESTADO_PEDIDO').value = pedidoData.id_ESTADO_PEDIDO || '';
                document.getElementById('modal_TOTAL_PRODUCTO').value = pedidoData.total_PRODUCTO || '';
                
                // Manejo de la fecha: Asegurar formato YYYY-MM-DD para el input type="date"
                if (pedidoData.fecha_ENTREGA) {
                    // Si el valor es una fecha de BD completa (timestamp), la formateamos
                    const date = new Date(pedidoData.fecha_ENTREGA);
                    const formattedDate = date.toISOString().split('T')[0];
                    document.getElementById('modal_FECHA_ENTREGA').value = formattedDate;
                } else {
                    document.getElementById('modal_FECHA_ENTREGA').value = '';
                }

                modal.show();
            });
        });
    });
</script>
@endpush