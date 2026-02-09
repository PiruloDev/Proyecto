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
        
        {{-- SIDEBAR FIJO DEL ADMINISTRADOR --}}
        @include('components.admin-sidebar') 
        
        {{-- CONTENIDO PRINCIPAL --}}
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
            
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2">Listado de Pedidos (Panel de Administración)</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-primary me-2" id="btn-create-pedido" style="background: #a67c52; border: none;">
                        <i class="fas fa-plus"></i> Crear Pedido
                    </button>
                    <a href="{{ route('admin.pedidos.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-sync"></i> Recargar Listado
                    </a>
                </div>
            </div>

            {{-- MENSAJES DE SESIÓN --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif  

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <section id="listado-pedidos" class="mb-5">
                <h3 class="mb-3 text-secondary">Pedidos Registrados</h3>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle shadow-sm rounded-3">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Empleado</th>
                                <th>Estado</th>
                                <th>Total</th>
                                <th>Ingreso</th>
                                <th>Entrega</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pedidos as $pedido)
                                <tr>
                                    <td>{{ $pedido['id_PEDIDO'] }}</td>
                                    <td>{{ $pedido['nombre_cliente'] ?? 'ID: '.$pedido['cliente_id'] }}</td>
                                    <td>{{ $pedido['nombre_empleado'] ?? 'ID: '.$pedido['empleado_id'] }}</td>
                                    <td>
                                        <span class="badge bg-info text-dark">
                                            {{ $pedido['nombre_estado'] ?? 'Estado: '.$pedido['estado_pedido_id'] }}
                                        </span>
                                    </td>
                                    {{-- Resaltar en rojo si el total llegara a ser negativo por error previo --}}
                                    <td class="{{ ($pedido['total_producto'] < 0) ? 'text-danger fw-bold' : '' }}">
                                        ${{ number_format($pedido['total_producto'] ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td>{{ $pedido['fecha_ingreso'] ?? 'N/A' }}</td>
                                    <td>{{ $pedido['fecha_entrega'] ?? 'N/A' }}</td>

                                    <td class="text-center text-nowrap">
                                        <button type="button" 
                                            class="btn btn-warning btn-sm me-1 btn-edit-pedido" 
                                            data-pedido="{{ json_encode($pedido) }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <form method="POST" action="{{ route('admin.pedidos.destroy', $pedido['id_PEDIDO']) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este pedido?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">No se encontraron pedidos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</div>

{{-- MODAL ÚNICO PARA CREAR Y EDITAR PEDIDO --}}
<div class="modal fade" id="pedidoModal" tabindex="-1" aria-labelledby="pedidoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 12px;">
            <div class="modal-header text-white" style="background: #a67c52; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title" id="pedidoModalLabel">Gestión de Pedido</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="pedidoForm" method="POST" action="">
                @csrf
                <input type="hidden" name="_method" value="POST" id="formMethod"> 
                
                <div class="modal-body row g-3">
                    <input type="hidden" name="id_PEDIDO" id="modal_id_PEDIDO">

                    <div class="col-md-6">
                        <label for="modal_ID_CLIENTE" class="form-label fw-bold">ID Cliente</label>
                        {{-- min="1" y bloqueo de teclado --}}
                        <input type="number" class="form-control" id="modal_ID_CLIENTE" name="ID_CLIENTE" min="1" onkeypress="return event.charCode >= 48" required>
                    </div>

                    <div class="col-md-6">
                        <label for="modal_ID_EMPLEADO" class="form-label fw-bold">ID Empleado</label>
                        {{-- min="1" y bloqueo de teclado --}}
                        <input type="number" class="form-control" id="modal_ID_EMPLEADO" name="ID_EMPLEADO" min="1" onkeypress="return event.charCode >= 48" required>
                    </div>

                    <div class="col-md-6">
                        <label for="modal_ID_ESTADO_PEDIDO" class="form-label fw-bold">ID Estado Pedido</label>
                        {{-- min="1" y bloqueo de teclado --}}
                        <input type="number" class="form-control" id="modal_ID_ESTADO_PEDIDO" name="ID_ESTADO_PEDIDO" min="1" onkeypress="return event.charCode >= 48" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="modal_FECHA_ENTREGA" class="form-label fw-bold">Fecha de Entrega</label>
                        <input type="date" class="form-control" id="modal_FECHA_ENTREGA" name="FECHA_ENTREGA">
                    </div>

                    <div class="col-md-6">
                        <label for="modal_TOTAL_PRODUCTO" class="form-label fw-bold">Total Producto ($)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            {{-- min="0", step para decimales y bloqueo de tecla "-" --}}
                            <input type="number" step="0.01" min="0" class="form-control" id="modal_TOTAL_PRODUCTO" name="TOTAL_PRODUCTO" 
                                   onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 46" required>
                        </div>
                        <div class="form-text small">No se permiten valores negativos.</div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white" id="modalSubmitButton" style="background: #a67c52;">Guardar Pedido</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

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
            form.action = "{{ route('admin.pedidos.store') }}";
            formMethod.value = 'POST';
            form.reset();
            // Asegurar que el total empiece en 0 si está vacío
            document.getElementById('modal_TOTAL_PRODUCTO').value = 0;
        }

        // Configurar botón "Crear Pedido"
        createButton.addEventListener('click', function() {
            setupCreate();
            modal.show();
        });

        // Configurar botones "Editar"
        document.querySelectorAll('.btn-edit-pedido').forEach(button => {
            button.addEventListener('click', function() {
                const pedidoData = JSON.parse(this.getAttribute('data-pedido'));
                
                modalTitle.textContent = 'Editar Pedido #' + pedidoData.id_PEDIDO;
                form.action = "{{ url('admin/pedidos') }}/" + pedidoData.id_PEDIDO;
                formMethod.value = 'PUT'; 

                // Llenar campos
                document.getElementById('modal_id_PEDIDO').value = pedidoData.id_PEDIDO || '';
                document.getElementById('modal_ID_CLIENTE').value = pedidoData.id_CLIENTE || '';
                document.getElementById('modal_ID_EMPLEADO').value = pedidoData.id_EMPLEADO || '';
                document.getElementById('modal_ID_ESTADO_PEDIDO').value = pedidoData.id_ESTADO_PEDIDO || '';
                document.getElementById('modal_TOTAL_PRODUCTO').value = pedidoData.total_PRODUCTO || 0;
                
                // Formatear fecha para el input date
                if (pedidoData.fecha_ENTREGA) {
                    const date = new Date(pedidoData.fecha_ENTREGA);
                    const formattedDate = date.toISOString().split('T')[0];
                    document.getElementById('modal_FECHA_ENTREGA').value = formattedDate;
                } else {
                    document.getElementById('modal_FECHA_ENTREGA').value = '';
                }

                modal.show();
            });
        });

        // Validación Extra: Si el usuario pega un valor negativo con el mouse, lo forzamos a 0
        document.getElementById('modal_TOTAL_PRODUCTO').addEventListener('change', function() {
            if (this.value < 0) this.value = 0;
        });
    });
</script>
@endpush