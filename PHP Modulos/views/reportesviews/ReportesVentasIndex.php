<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Variables */
        :root {
            --primary-color: #8C5A37;
            --primary-hover: #6E462A;
            --text-dark: #3D2C21;
            --text-light: #6B5B51;
            --border-color: #D3C5BC;
            --container-bg: rgba(255, 255, 255, 0.75);
            --white: #ffffff;
            --shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.7);
            border-radius: 15px;
            padding: 10px;
            margin-bottom: 30px;
        }

        body {
            background-image: url('https://www.revistamag.com/wp-content/uploads/2024/05/Pan-Freepik.jpg');
            background-size: cover;
            background-attachment: fixed;
            font-family: 'Lato', sans-serif;
        }

        h1 { font-family: 'Playfair Display', serif; 
            color: var(--text-dark); 
            margin-bottom: 30px; text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.6); 
            font-size: 3.5rem;
        }

        .card {
            background: var(--container-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: var(--border-radius);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 20px;
            margin-bottom: 30px;
            margin-left: auto;
            margin-right: auto;
        }

        input, select {
            padding: 12px;
            border: 2px solid var(--border-color);
            background-color: rgba(255, 255, 255, 0.5);
            border-radius: 8px;
            font-size: 16px;
            color: var(--text-dark);
            transition: var(--transition);
        }

        input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 8px rgba(140, 90, 55, 0.4);
        }

        .btn {
            padding: 12px;
            background-color: var(--primary-color);
            border: none;
            border-radius: 8px;
            font-size: 16px;
            color: #fff;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(140, 90, 55, 0.3);
        }

        .table thead {
            background-color: var(--primary-color);
            color: #fff;
        }

        .alert-info {
            background-color: #f8f3ef;
            border-color: var(--border-color);
            color: var(--text-dark);
        }
        .background-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(6px);
            z-index: 0;
        }
        .contenedor-central {
            max-width: 1000px; 
            margin: 0 auto;
            padding: 20px;
        }

        /* Estilos para el modal */
        .modal-content {
            background: var(--container-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .modal-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 8px 8px 0 0;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px;
            border-bottom: 1px solid var(--border-color);
        }

        .detail-label {
            font-weight: bold;
            color: var(--primary-color);
        }

        .detail-value {
            color: var(--text-dark);
        }

        .btn-info {
            background-color: #5A8CB3;
            border-color: #5A8CB3;
        }

        .btn-info:hover {
            background-color: #4A7CA3;
            border-color: #4A7CA3;
        }
    </style>
</head>
<body>

<div class="background-overlay"></div>

<div class="contenedor-central py-4" style="position: relative; z-index: 1;">
    <h1 class="text-center">Gestión de Ventas</h1>

    <nav class="navbar navbar-expand-lg" style="background: var(--container-bg);">
        <div class="container">
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="?">Dashboard</a>
                <a class="nav-link" href="?route=productos">Productos Top</a>
                <a class="nav-link" href="?route=ventas">Usuarios</a>
            </div>
        </div>
    </nav>

    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-info text-center">
            <?= $mensaje ?>
        </div>
    <?php endif; ?>

    <!-- Card para agregar venta -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white" style="background-color: var(--primary-color);">
            Agregar Venta
        </div>
        <div class="card-body">
            <form method="POST" class="row g-3">
                <input type="hidden" name="accion" value="agregar">
                <div class="col-md-3">
                    <input type="number" name="idCliente" class="form-control" placeholder="ID Cliente" required>
                </div>
                <div class="col-md-3">
                    <input type="number" name="idPedido" class="form-control" placeholder="ID Pedido" required>
                </div>
                <div class="col-md-3">
                    <input type="date" name="fecha" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <input type="time" name="hora" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <input type="number" step="0.01" name="totalFactura" class="form-control" placeholder="Total Factura" required>
                </div>
                <div class="class=btn btn-danger btn-sm">
                    <button type="submit" class="btn w-100">Agregar Venta</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de ventas -->
    <div class="card shadow-sm">
        <div class="card-header text-white" style="background-color: var(--primary-hover);">
            Lista de Ventas
        </div>
        <div class="card-body">
            <?php if (!empty($ventas) && is_array($ventas)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID Factura</th>
                                <th>ID Cliente</th>
                                <th>ID Pedido</th>
                                <th>Fecha Facturación</th>
                                <th>Total Factura</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
    <?php foreach ($ventas as $venta): ?>
    <tr>
        <td><?= htmlspecialchars($venta['idFactura']) ?></td>
        <td>
            <input type="number" 
                   id="cliente-<?= $venta['idFactura'] ?>" 
                   value="<?= htmlspecialchars($venta['idCliente']) ?>" 
                   class="form-control form-control-sm" 
                   style="max-width: 80px;">
        </td>
        <td>
            <input type="number" 
                   id="pedido-<?= $venta['idFactura'] ?>" 
                   value="<?= htmlspecialchars($venta['idPedido']) ?>" 
                   class="form-control form-control-sm" 
                   style="max-width: 80px;">
        </td>
        <td>
            <input type="datetime-local" 
                   id="fecha-<?= $venta['idFactura'] ?>" 
                   value="<?= date('Y-m-d\TH:i', strtotime($venta['fechaFacturacion'])) ?>" 
                   class="form-control form-control-sm" 
                   style="min-width: 200px;">
        </td>
        <td>
    <input type="number" 
           step="0.01" 
           id="total-<?= $venta['idFactura'] ?>" 
           value="<?= htmlspecialchars($venta['totalFactura']) ?>" 
           class="form-control form-control-sm" 
           style="min-width: 85px; width: 100%;">
</td>
        <td class="text-center" style="min-width: 250px;">
            <div class="d-flex justify-content-center gap-2 flex-nowrap">
                <button type="button" class="btn btn-danger btn-sm" 
                        onclick="verFactura(<?= htmlspecialchars(json_encode($venta)) ?>)">
                    Ver
                </button>
                <button type="button" class="btn btn-danger btn-sm"
                        onclick="actualizarVenta(<?= $venta['idFactura'] ?>)">
                    Actualizar
                </button>
                <button type="button" class="btn btn-danger btn-sm" 
                        onclick="eliminarVenta(<?= $venta['idFactura'] ?>)">
                    Eliminar
                </button>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted text-center">No hay ventas registradas.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para ver detalles de la factura -->
<div class="modal fade" id="modalVerFactura" tabindex="-1" aria-labelledby="modalVerFacturaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalVerFacturaLabel">Detalles de la Factura</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="detail-row">
                    <span class="detail-label">ID Factura:</span>
                    <span class="detail-value" id="detalle-idFactura"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">ID Cliente:</span>
                    <span class="detail-value" id="detalle-idCliente"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">ID Pedido:</span>
                    <span class="detail-value" id="detalle-idPedido"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Fecha de Facturación:</span>
                    <span class="detail-value" id="detalle-fechaFacturacion"></span>
                </div>
                <div class="detail-row" style="border-bottom: none;">
                    <span class="detail-label">Total Factura:</span>
                    <span class="detail-value" id="detalle-totalFactura" style="font-size: 1.2em; font-weight: bold;"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function actualizarVenta(idFactura) {
    const cliente = document.getElementById('cliente-' + idFactura).value;
    const pedido = document.getElementById('pedido-' + idFactura).value;
    const fecha = document.getElementById('fecha-' + idFactura).value;
    const total = document.getElementById('total-' + idFactura).value;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';
    
    const inputs = {
        'accion': 'actualizar',
        'id': idFactura,
        'idCliente': cliente,
        'idPedido': pedido,
        'fechaFacturacion': fecha.replace('T', ' ') + ':00',
        'totalFactura': total
    };
    
    for (const [name, value] of Object.entries(inputs)) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    }
    
    document.body.appendChild(form);
    form.submit();
}

function eliminarVenta(idFactura) {
    if (!confirm('¿Seguro que deseas eliminar esta venta?')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';
    
    const accionInput = document.createElement('input');
    accionInput.type = 'hidden';
    accionInput.name = 'accion';
    accionInput.value = 'eliminar';
    
    const idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'id';
    idInput.value = idFactura;
    
    form.appendChild(accionInput);
    form.appendChild(idInput);
    document.body.appendChild(form);
    form.submit();
}

function verFactura(venta) {
    document.getElementById('detalle-idFactura').textContent = venta.idFactura;
    document.getElementById('detalle-idCliente').textContent = venta.idCliente;
    document.getElementById('detalle-idPedido').textContent = venta.idPedido;
    document.getElementById('detalle-fechaFacturacion').textContent = venta.fechaFacturacion;
    document.getElementById('detalle-totalFactura').textContent = '$' + parseFloat(venta.totalFactura).toFixed(2);
    
    const modal = new bootstrap.Modal(document.getElementById('modalVerFactura'));
    modal.show();
}

</script>
</body>
</html>