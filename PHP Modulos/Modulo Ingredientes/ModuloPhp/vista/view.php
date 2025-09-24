<?php
// Variables que vienen del controller
$mensaje = $mensaje ?? '';
$detalles = $detalles ?? [];
$detalle = $detalle ?? null; // Para edición
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Detalles de Pedido</title>
</head>
<body>
    <h1>Gestión de Detalles de Pedido</h1>

    <!-- Mostrar mensajes -->
    <?php if (!empty($mensaje)): ?>
        <div><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <!-- Lista de detalles (GET) -->
    <h2>Detalles Registrados</h2>
    <?php if (is_array($detalles) && !empty($detalles)): ?>
        <?php foreach ($detalles as $d): ?>
            <div class="detalle-item" style="border:1px solid #ccc; margin:5px; padding:5px;">
                <strong>ID Detalle:</strong> <?= htmlspecialchars($d['idDetalle'] ?? 'N/A') ?><br>
                <strong>ID Pedido:</strong> <?= htmlspecialchars($d['idPedido'] ?? 'N/A') ?><br>
                <strong>ID Producto:</strong> <?= htmlspecialchars($d['idProducto'] ?? 'N/A') ?><br>
                <strong>Cantidad:</strong> <?= htmlspecialchars($d['cantidadProducto'] ?? 'N/A') ?><br>
                <strong>Precio Unitario:</strong> <?= htmlspecialchars($d['precioUnitario'] ?? 'N/A') ?><br>
                <strong>Subtotal:</strong> <?= htmlspecialchars($d['subtotal'] ?? 'N/A') ?><br>

                <!-- Acciones -->
                <a href="?modulo=detallePedidos&accion=editar
                    &id=<?= urlencode($d['idDetalle']) ?>
                    &idPedido=<?= urlencode($d['idPedido']) ?>
                    &idProducto=<?= urlencode($d['idProducto']) ?>
                    &cantidadProducto=<?= urlencode($d['cantidadProducto']) ?>
                    &precioUnitario=<?= urlencode($d['precioUnitario']) ?>
                    &subtotal=<?= urlencode($d['subtotal']) ?>">
                    ✏️ Editar
                </a>
                |
                <a href="?modulo=detallePedidos&accion=eliminar&id=<?= urlencode($d['idDetalle']) ?>"
                   onclick="return confirm('¿Seguro de eliminar este detalle?');">
                    Eliminar
                </a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="color:red;">No hay detalles registrados o error al obtener los datos.</p>
    <?php endif; ?>

    <hr>

    <!-- POST: Agregar detalle -->
    <h2>Agregar Nuevo Detalle</h2>
    <form method="POST" action="?modulo=detallePedidos&accion=agregar">
        <div>
            <label for="idPedido">ID Pedido:</label>
            <input type="number" name="idPedido" id="idPedido" required>
        </div>
        <div>
            <label for="idProducto">ID Producto:</label>
            <input type="number" name="idProducto" id="idProducto" required>
        </div>
        <div>
            <label for="cantidadProducto">Cantidad:</label>
            <input type="number" name="cantidadProducto" id="cantidadProducto" required>
        </div>
        <div>
            <label for="precioUnitario">Precio Unitario:</label>
            <input type="number" step="0.01" name="precioUnitario" id="precioUnitario" required>
        </div>
        <div>
            <label for="subtotal">Subtotal:</label>
            <input type="number" step="0.01" name="subtotal" id="subtotal" required>
        </div>
        <input type="submit" value=" Agregar Detalle">
    </form>

    <hr>

    <!-- PUT: Actualizar detalle (si se seleccionó editar) -->
    <?php if (!empty($detalle)): ?>
        <h2>Actualizar Detalle</h2>
        <form method="POST" action="?modulo=detallePedidos&accion=editar">
            <input type="hidden" name="idDetalle" value="<?= htmlspecialchars($detalle['idDetalle']) ?>">

            <div>
                <label for="idPedido">ID Pedido:</label>
                <input type="number" name="idPedido" value="<?= htmlspecialchars($detalle['idPedido']) ?>" required>
            </div>
            <div>
                <label for="idProducto">ID Producto:</label>
                <input type="number" name="idProducto" value="<?= htmlspecialchars($detalle['idProducto']) ?>" required>
            </div>
            <div>
                <label for="cantidadProducto">Cantidad:</label>
                <input type="number" name="cantidadProducto" value="<?= htmlspecialchars($detalle['cantidadProducto']) ?>" required>
            </div>
            <div>
                <label for="precioUnitario">Precio Unitario:</label>
                <input type="number" step="0.01" name="precioUnitario" value="<?= htmlspecialchars($detalle['precioUnitario']) ?>" required>
            </div>
            <div>
                <label for="subtotal">Subtotal:</label>
                <input type="number" step="0.01" name="subtotal" value="<?= htmlspecialchars($detalle['subtotal']) ?>" required>
            </div>
            <input type="submit" value=" Actualizar Detalle">
        </form>
    <?php endif; ?>

</body>
</html>
