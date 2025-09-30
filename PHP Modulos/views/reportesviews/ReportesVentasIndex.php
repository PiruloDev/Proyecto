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
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            z-index: 0;
        }
        .contenedor-central {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
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
                <a class="nav-link" href="?route=usuarios">Usuarios</a>
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
                <div class="col-md-8 text-end">
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
                            <td><?= htmlspecialchars($venta['idCliente']) ?></td>
                            <td><?= htmlspecialchars($venta['idPedido']) ?></td>
                            <td><?= htmlspecialchars($venta['fechaFacturacion']) ?></td>
                            <td>
                                <form method="POST" class="d-flex">
                                    <input type="hidden" name="accion" value="actualizar">
                                    <input type="hidden" name="id" value="<?= $venta['idFactura'] ?>">
                                    <input type="number" step="0.01" name="totalFactura"
                                           value="<?= htmlspecialchars($venta['totalFactura']) ?>"
                                           class="form-control me-2" required>
                                    <button type="submit" class="btn btn-sm">Actualizar</button>
                                </form>
                            </td>
                            <td class="text-center">
                                <a href="ReportesVentasController.php?eliminar=<?= $venta['idFactura'] ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('¿Seguro que deseas eliminar esta venta?');">
                                   Eliminar
                                </a>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
