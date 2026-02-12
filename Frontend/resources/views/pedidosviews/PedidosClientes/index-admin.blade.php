@extends('layouts.app')

@section('title', 'Gestión de Pedidos (Administrador)')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
      
        body, html {
            height: 100%;
            overflow: hidden; 
        }

        .container-fluid, .row {
            height: 100vh;
        }

        .sidebar {
            height: 100vh;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .main-content {
            height: 100vh;
            overflow-y: auto; 
            background-color: #fdfaf6;
            padding: 0 !important;
        }

        .sticky-header-section {
            position: sticky;
            top: 0;
            z-index: 150; 
            background-color: #fdfaf6; 
            padding: 1.5rem 1.5rem 0 1.5rem;
            border-bottom: 1px solid #dee2e6;
        }

        .table-responsive {
            overflow: visible !important;
            padding: 0 1.5rem 1.5rem 1.5rem;
        }

        .table {
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }

        .table thead th {
            position: sticky;
            top: 103px; 
            z-index: 100;
            background-color: #ffffff !important;
            border-bottom: 2px solid #dee2e6 !important;
            box-shadow: 0 2px 2px -1px rgba(0,0,0,0.1);
        }

        .table-bordered th, .table-bordered td {
            border: 1px solid #dee2e6 !important;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        
        @include('components.admin-sidebar') 
        
        <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
            
            <div class="sticky-header-section">
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

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif  

                <h3 class="mb-3 text-secondary">Pedidos Registrados</h3>
            </div>

            <section id="listado-pedidos" class="mb-5 mt-3">
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
                        <input type="number" class="form-control" id="modal_ID_CLIENTE" name="ID_CLIENTE" min="1" onkeypress="return event.charCode >= 48" required>
                    </div>

                    <div class="col-md-6">
                        <label for="modal_ID_EMPLEADO" class="form-label fw-bold">ID Empleado</label>
                        <input type="number" class="form-control" id="modal_ID_EMPLEADO" name="ID_EMPLEADO" min="1" onkeypress="return event.charCode >= 48" required>
                    </div>

                    <div class="col-md-6">
                        <label for="modal_ID_ESTADO_PEDIDO" class="form-label fw-bold">ID Estado Pedido</label>
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

        function setupCreate() {
            modalTitle.textContent = 'Crear Nuevo Pedido';
            form.action = "{{ route('admin.pedidos.store') }}";
            formMethod.value = 'POST';
            form.reset();
            document.getElementById('modal_TOTAL_PRODUCTO').value = 0;
        }

        createButton.addEventListener('click', function() {
            setupCreate();
            modal.show();
        });

        document.querySelectorAll('.btn-edit-pedido').forEach(button => {
            button.addEventListener('click', function() {
                const pedidoData = JSON.parse(this.getAttribute('data-pedido'));
                
                modalTitle.textContent = 'Editar Pedido #' + pedidoData.id_PEDIDO;
                form.action = "{{ url('admin/pedidos') }}/" + pedidoData.id_PEDIDO;
                formMethod.value = 'PUT'; 

                document.getElementById('modal_id_PEDIDO').value = pedidoData.id_PEDIDO || '';
                document.getElementById('modal_ID_CLIENTE').value = pedidoData.id_CLIENTE || '';
                document.getElementById('modal_ID_EMPLEADO').value = pedidoData.id_EMPLEADO || '';
                document.getElementById('modal_ID_ESTADO_PEDIDO').value = pedidoData.id_ESTADO_PEDIDO || '';
                document.getElementById('modal_TOTAL_PRODUCTO').value = pedidoData.total_PRODUCTO || 0;
                
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

        document.getElementById('modal_TOTAL_PRODUCTO').addEventListener('change', function() {
            if (this.value < 0) this.value = 0;
        });
    });
</script>
@endpush