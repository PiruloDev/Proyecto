{{-- resources/views/inventarioviews/pedidoproveedores/show.blade.php (Ajustado a Dashboard) --}}

@extends('layouts.app') 

@section('title', 'Detalle del Pedido N° ' . ($pedido['numeroPedido'] ?? 'N/A') . ' - El Castillo del Pan')

{{-- Importamos los mismos estilos del layout principal y Font Awesome --}}
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    {{-- Si tienes CSS específico del módulo, inclúyelo aquí --}}
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        
        {{-- Side Bar: Usamos el componente de dashboard --}}
        @include('components.admin-sidebar') 
        
        {{-- CONTENIDO PRINCIPAL --}}
        <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
            
            {{-- Título y Botón Volver --}}
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2">Detalle del Pedido a Proveedor</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('pedidoproveedores.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver al Listado
                    </a>
                </div>
            </div>

            <h2 class="text-primary mb-4">
                <i class="fas fa-truck-loading"></i> Pedido N° {{ $pedido['numeroPedido'] ?? 'N/A' }}
            </h2>

            {{-- ENCABEZADO DEL PEDIDO --}}
            <div class="card shadow-lg mb-5 border-0">
                <div class="card-header bg-primary text-white h5">
                    <i class="fas fa-info-circle"></i> Información General
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>ID Interno:</strong> <span class="text-muted">{{ $pedido['idPedidoProv'] ?? 'N/A' }}</span></p>
                            <p class="mb-2"><strong>Número de Pedido (Ref):</strong> <span class="text-dark fw-bold">{{ $pedido['numeroPedido'] ?? 'N/A' }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>ID Proveedor:</strong> <span class="text-muted">{{ $pedido['idProveedor'] ?? 'N/A' }}</span></p>
                            <p class="mb-2">
                                <strong>Estado:</strong> 
                                @php
                                    $estado = $pedido['estadoPedido'] ?? 'N/A';
                                    $badgeClass = match ($estado) {
                                        'PENDIENTE' => 'bg-warning text-dark',
                                        'COMPLETADO' => 'bg-success',
                                        'CANCELADO' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} fs-6">{{ $estado }}</span>
                            </p>
                            <p class="mb-0">
                                <strong>Fecha de Pedido:</strong> 
                                @if(isset($pedido['fechaPedido']))
                                    {{ \Carbon\Carbon::parse($pedido['fechaPedido'])->format('d/m/Y H:i') }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DETALLES DEL PEDIDO (INGREDIENTES) --}}
            <section id="detalles-pedido" class="mb-5">
                <h3 class="mb-3 text-secondary"><i class="fas fa-boxes"></i> Ingredientes Solicitados</h3>

                @if (empty($pedido['detalles']))
                    <div class="alert alert-warning shadow-sm">
                        <i class="fas fa-exclamation-triangle"></i> Este pedido no tiene detalles de ingredientes registrados.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle shadow-sm rounded-3">
                            <thead class="table-dark"> {{-- Usamos un color oscuro para la cabecera --}}
                                <tr>
                                    <th style="width: 10%;">ID Detalle</th>
                                    <th style="width: 10%;">ID Ingrediente</th>
                                    <th>Nombre Ingrediente</th>
                                    <th style="width: 15%;">Cantidad</th>
                                    <th style="width: 15%;" class="text-end">Precio Unitario</th>
                                    <th style="width: 15%;" class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalGeneral = 0;
                                @endphp

                                @foreach($pedido['detalles'] as $detalle)
                                    <tr>
                                        <td>{{ $detalle['idDetalleProv'] ?? 'N/A' }}</td>
                                        <td>{{ $detalle['idIngrediente'] ?? 'N/A' }}</td>
                                        <td>{{ $detalle['nombreIngrediente'] ?? 'Sin Nombre' }}</td>
                                        <td>{{ $detalle['cantidad'] ?? 0 }}</td>
                                        <td class="text-end text-success">${{ number_format($detalle['precioUnitario'] ?? 0, 2) }}</td>
                                        <td class="text-end fw-bold">${{ number_format($detalle['subtotal'] ?? 0, 2) }}</td>
                                    </tr>
                                    @php
                                        $totalGeneral += $detalle['subtotal'] ?? 0;
                                    @endphp
                                @endforeach
                                
                                {{-- FILA DEL TOTAL --}}
                                <tr class="table-secondary"> {{-- Resaltamos la fila del total --}}
                                    <td colspan="5" class="text-end fw-bold h5 mb-0">TOTAL GENERAL (USD)</td>
                                    <td class="text-end fw-bold h4 mb-0 text-primary">${{ number_format($totalGeneral, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </main>
    </div>
</div>
@endsection

@push('scripts')
    {{-- No hay scripts necesarios para una vista de 'show' simple. --}}
@endpush