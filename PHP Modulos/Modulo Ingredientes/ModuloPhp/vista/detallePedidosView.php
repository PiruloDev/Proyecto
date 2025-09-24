<?php
$detalles = $detalles ?? [];
$modo = $modo ?? "listar";
$detalleEditar = $detalleEditar ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Detalles de Pedido</title>
</head>
<body>
    <h1>Gestión de Detalles de Pedido</h1>

    <!-- LISTA -->
    <h2>Detalles Registrados</h2>
    <?php if (!empty($detalles)): ?>
        <?php foreach ($detalles as $d): ?>
            <div style="border:1px solid #ccc; margin:5px; padding:5px;">
                <strong>ID Detalle:</strong> <?= htmlspecialchars($d['idDetalle']) ?><br>
                <strong>ID Pedido:</strong> <?= htmlspecialchars($d['idPedido']) ?><br>
                <strong>ID Producto:</strong> <?= htmlspecialchars($d['idProducto']) ?><br>
                <strong>Cantidad:</strong> <?= htmlspecialchars($d['cantidadProducto']) ?><br>
                <strong>Precio Unitario:</strong> <?= htmlspecialchars($d['precioUnitario']) ?><br>
                <strong>Subtotal:</strong> <?= htmlspecialchars($d['subtotal']) ?><br>

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
                   onclick="return confirm('¿Eliminar este detalle?');">🗑️ Eliminar</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No hay registros.</p>
    <?php endif; ?>

    <hr>

    <!-- FORMULARIO AGREGAR -->
    <?php if ($modo === "listar"): ?>
        <h2>Agregar Nuevo Detalle</h2>
        <form method="POST" action="?modulo=detallePedidos&accion=agregar">
            <label>ID Pedido:</label><br>
            <input type="number" name="idPedido" required><br>
            <label>ID Producto:</label><br>
            <input type="number" name="idProducto" required><br>
            <label>Cantidad:</label><br>
            <input type="number" name="cantidadProducto" required><br>
            <label>Precio Unitario:</label><br>
            <input type="number" step="0.01" name="precioUnitario" required><br>
            <label>Subtotal:</label><br>
            <input type="number" step="0.01" name="subtotal" required><br><br>
            <input type="submit" value="➕ Agregar">
        </form>
    <?php endif; ?>

    <!-- FORMULARIO EDITAR -->
    <?php if ($modo === "editar" && $detalleEditar): ?>
        <h2>Editar Detalle</h2>
        <form method="POST" action="?modulo=detallePedidos&accion=editar">
            <input type="hidden" name="idDetalle" value="<?= htmlspecialchars($detalleEditar['idDetalle']) ?>">

            <label>ID Pedido:</label><br>
            <input type="number" name="idPedido" value="<?= htmlspecialchars($detalleEditar['idPedido']) ?>" required><br>
            <label>ID Producto:</label><br>
            <input type="number" name="idProducto" value="<?= htmlspecialchars($detalleEditar['idProducto']) ?>" required><br>
            <label>Cantidad:</label><br>
            <input type="number" name="cantidadProducto" value="<?= htmlspecialchars($detalleEditar['cantidadProducto']) ?>" required><br>
            <label>Precio Unitario:</label><br>
            <input type="number" step="0.01" name="precioUnitario" value="<?= htmlspecialchars($detalleEditar['precioUnitario']) ?>" required><br>
            <label>Subtotal:</label><br>
            <input type="number" step="0.01" name="subtotal" value="<?= htmlspecialchars($detalleEditar['subtotal']) ?>" required><br><br>
            <input type="submit" value="💾 Guardar Cambios">
        </form>
        <br>
        <a href="index.php?modulo=detallePedidos&accion=listar">⬅️ Cancelar edición</a>
    <?php endif; ?>

</body>
</html>
