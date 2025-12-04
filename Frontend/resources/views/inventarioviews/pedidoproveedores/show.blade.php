{{-- resources/views/inventarioviews/pedidoproveedores/show.blade.php --}}

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Pedido #{{ $pedido['numeroPedido'] ?? 'N/A' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    {{-- Asegúrate de incluir aquí tu CSS específico, como stylemoduloinv.css --}}
</head>

<body>
    <div class="container-fluid">
        <div class="row g-0">
            @include('partials.sidebar-inventario')

            <div class="col-md-9 col-lg-10 main-content">

                <h1 class="mt-3">Detalle del Pedido a Proveedor</h1>
                <h2 class="text-primary">Pedido N° {{ $pedido['numeroPedido'] ?? 'N/A' }}</h2>

                <div class="mb-3">
                    <a href="{{ route('pedidoproveedores.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver al Listado
                    </a>
                </div>

                {{-- ENCABEZADO DEL PEDIDO --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        Información General
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ID Interno:</strong> {{ $pedido['idPedidoProv'] ?? 'N/A' }}</p>
                                <p><strong>Número de Pedido:</strong> {{ $pedido['numeroPedido'] ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>ID Proveedor:</strong> {{ $pedido['idProveedor'] ?? 'N/A' }}</p>
                                <p><strong>Estado:</strong> 
                                    <span class="badge bg-{{ ($pedido['estadoPedido'] ?? '') == 'COMPLETADO' ? 'success' : 'warning' }}">
                                        {{ $pedido['estadoPedido'] ?? 'N/A' }}
                                    </span>
                                </p>
                                <p><strong>Fecha de Pedido:</strong> 
                                    @if(isset($pedido['fechaPedido']))
                                        {{ \Carbon\Carbon::parse($pedido['fechaPedido'])->format('d/m/Y H:i:s') }}
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
                    <h3>Ingredientes Solicitados</h3>

                    @if (empty($pedido['detalles']))
                        <div class="alert alert-warning">Este pedido no tiene detalles de ingredientes registrados.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID Detalle</th>
                                        <th>ID Ingrediente</th>
                                        <th>Nombre Ingrediente</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unitario</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Calcular el Total General
                                        $totalGeneral = 0;
                                    @endphp

                                    @foreach($pedido['detalles'] as $detalle)
                                        <tr>
                                            <td>{{ $detalle['idDetalleProv'] ?? 'N/A' }}</td>
                                            <td>{{ $detalle['idIngrediente'] ?? 'N/A' }}</td>
                                            {{-- Se asume que Spring trae el campo 'nombreIngrediente' --}}
                                            <td>{{ $detalle['nombreIngrediente'] ?? 'Sin Nombre' }}</td>
                                            <td>{{ $detalle['cantidad'] ?? 0 }}</td>
                                            <td>${{ number_format($detalle['precioUnitario'] ?? 0, 2) }}</td>
                                            <td>${{ number_format($detalle['subtotal'] ?? 0, 2) }}</td>
                                        </tr>
                                        @php
                                            // Suma el subtotal al total general (asegúrate de que es un valor numérico)
                                            $totalGeneral += $detalle['subtotal'] ?? 0;
                                        @endphp
                                    @endforeach
                                    
                                    {{-- FILA DEL TOTAL --}}
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">TOTAL GENERAL</td>
                                        <td class="fw-bold">${{ number_format($totalGeneral, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>