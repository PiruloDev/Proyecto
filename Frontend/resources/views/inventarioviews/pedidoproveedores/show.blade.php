{{-- resources/views/inventarioviews/pedidoproveedores/show.blade.php --}}

@extends('layouts.app') 

@section('title', 'Detalle de Pedido - El Castillo del Pan')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/variables.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/dashboard-admin.css') }}" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* Scroll interno y fondo */
        .main-content {
            height: 100vh;
            overflow-y: auto;
            background: #fdfbf9;
            padding-bottom: 50px;
        }

        /* CARD PRINCIPAL (FICHA) */
        .pedido-container {
            background: white;
            border-radius: 20px;
            border: none;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(93, 64, 55, 0.08) !important;
            margin-top: 10px;
        }

        /* Encabezado de la Card con gradiente suave café */
        .pedido-header {
            background: linear-gradient(45deg, #5d4037, #8d6e63);
            color: white;
            padding: 2.5rem 2rem;
            border-bottom: 5px solid #d7ccc8;
        }

        .info-label {
            color: #8d6e63;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .info-value {
            color: #3e2723;
            font-weight: 600;
            font-size: 1.05rem;
        }

        /* TABLA DE PRODUCTOS */
        .table-custom thead {
            background-color: #fcfaf8;
        }

        .table-custom th {
            color: #5d4037;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            border-top: none;
            padding: 15px;
        }

        .table-custom td {
            padding: 15px;
            border-bottom: 1px solid #f1eeed;
        }

        /* CAJA DE TOTALES */
        .total-box {
            background-color: #fdfbf9;
            border-radius: 15px;
            padding: 25px;
            border: 1px solid #eee;
        }

        .btn-brown-outline {
            color: #5d4037;
            border: 2px solid #5d4037;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-brown-outline:hover {
            background-color: #5d4037;
            color: white;
        }

        /* Scrollbar estética */
        .main-content::-webkit-scrollbar { width: 6px; }
        .main-content::-webkit-scrollbar-thumb { background: #d7ccc8; border-radius: 10px; }

        @media print {
            .btn-back-print, .admin-sidebar { display: none !important; }
            .main-content { overflow: visible !important; height: auto !important; padding: 0 !important; }
            .pedido-container { box-shadow: none !important; border: 1px solid #eee; }
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('components.admin-sidebar') 

        <main class="col-md-9 ms-sm-auto col-lg-10 px-4 main-content">
            
            {{-- Acciones --}}
            <div class="d-flex justify-content-between align-items-center pt-4 pb-2 mb-4 btn-back-print">
                <a href="{{ route('pedidoproveedores.index') }}" class="btn btn-brown-outline btn-sm rounded-pill px-4">
                    <i class="fas fa-chevron-left me-2"></i> Volver
                </a>
                <button onclick="window.print()" class="btn btn-dark btn-sm rounded-pill px-4 shadow-sm">
                    <i class="fas fa-print me-2"></i> Imprimir Comprobante
                </button>
            </div>

            {{-- FICHA --}}
            <div class="pedido-container mb-5">
                
                <div class="pedido-header">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <div class="d-flex align-items-center">
                                <div class="bg-white p-3 rounded-circle me-3 d-none d-md-block shadow-sm">
                                    <i class="fas fa-bread-slice fa-lg" style="color: #5d4037;"></i>
                                </div>
                                <div>
                                    <p class="text-white-50 mb-0 small fw-bold text-uppercase">Orden de Compra Interna</p>
                                    <h2 class="fw-bold mb-0">N° {{ $pedido['numeroPedido'] ?? 'S/N' }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 text-md-end mt-3 mt-md-0">
                            @php
                                $estado = strtoupper($pedido['estadoPedido'] ?? 'PENDIENTE');
                                $badgeStyle = match ($estado) {
                                    'PENDIENTE' => 'background: #fff3cd; color: #856404;',
                                    'COMPLETADO' => 'background: #d4edda; color: #155724;',
                                    'CANCELADO' => 'background: #f8d7da; color: #721c24;',
                                    default => 'background: #e2e3e5; color: #383d41;',
                                };
                            @endphp
                            <span class="badge fs-6 px-4 py-2 rounded-pill shadow-sm" style="{{ $badgeStyle }}">
                                <i class="fas fa-dot-circle me-2"></i>{{ $estado }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-4 p-md-5">
                    {{-- Datos del Pedido --}}
                    <div class="row mb-5 g-4">
                        <div class="col-sm-4">
                            <div class="info-label">Proveedor</div>
                            <div class="info-value">
                                <i class="fas fa-store-alt me-2 text-muted"></i>
                                {{ $pedido['proveedor']['nombreProv'] ?? 'Proveedor ID: ' . ($pedido['idProveedor'] ?? 'N/A') }}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="info-label">Fecha de Registro</div>
                            <div class="info-value">
                                <i class="far fa-calendar-check me-2 text-muted"></i>
                                {{ isset($pedido['fechaPedido']) ? \Carbon\Carbon::parse($pedido['fechaPedido'])->format('d/m/Y') : 'N/A' }}
                            </div>
                        </div>
                        <div class="col-sm-4 text-sm-end">
                            <div class="info-label">Referencia del Sistema</div>
                            <div class="info-value text-muted font-monospace">REF-{{ str_pad($pedido['idPedidoProv'] ?? 0, 5, '0', STR_PAD_LEFT) }}</div>
                        </div>
                    </div>

                    {{-- Tabla --}}
                    <div class="table-responsive mb-5">
                        <table class="table table-custom align-middle">
                            <thead>
                                <tr>
                                    <th>Ingrediente / Insumo</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $total = 0; @endphp
                                @forelse($pedido['detalles'] ?? [] as $detalle)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $detalle['nombreIngrediente'] ?? 'Ingrediente #' . $detalle['idIngrediente'] }}</div>
                                            <small class="text-muted">Cód: {{ $detalle['idIngrediente'] }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border px-3 py-2 fw-normal">
                                                {{ $detalle['cantidad'] }} uds
                                            </span>
                                        </td>
                                        <td class="text-end text-muted">${{ number_format($detalle['precioUnitario'], 2) }}</td>
                                        <td class="text-end fw-bold text-dark">${{ number_format($detalle['subtotal'], 2) }}</td>
                                    </tr>
                                    @php $total += $detalle['subtotal']; @endphp
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted italic">No hay detalles disponibles.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Resumen --}}
                    <div class="row justify-content-end">
                        <div class="col-md-5">
                            <div class="total-box shadow-sm">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small fw-bold">SUMA DE PRODUCTOS</span>
                                    <span class="fw-bold text-dark">${{ number_format($total, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted small fw-bold">CARGOS ADICIONALES</span>
                                    <span class="text-muted">$0.00</span>
                                </div>
                                <div class="border-top pt-3 d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 fw-bold" style="color: #5d4037;">TOTAL PEDIDO</h5>
                                    <h4 class="mb-0 fw-bold text-success">${{ number_format($total, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-light p-4 text-center border-top">
                    <p class="text-muted small mb-0">
                        Este documento es una representación visual de la orden de compra almacenada en el sistema de **El Castillo del Pan**.
                    </p>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection