<h1>Gestión de Detalle de Pedidos</h1>

<!-- Listado -->
<h2>Lista de Detalles</h2>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th><th>ID Pedido</th><th>ID Producto</th>
        <th>Cantidad</th><th>Precio Unitario</th><th>Subtotal</th>
    </tr>
    <?php if (!empty($detalles)): ?>
        <?php foreach ($detalles as $d): ?>
            <tr>
                <td><?= htmlspecialchars($d['idDetalle']) ?></td>
                <td><?= htmlspecialchars($d['idPedido']) ?></td>
                <td><?= htmlspecialchars($d['idProducto']) ?></td>
                <td><?= htmlspecialchars($d['cantidadProducto']) ?></td>
                <td><?= htmlspecialchars($d['precioUnitario']) ?></td>
                <td><?= htmlspecialchars($d['subtotal']) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="6">No hay registros</td></tr>
    <?php endif; ?>
</table>

<hr>

<!-- Crear -->
<h2>Crear Detalle</h2>
<form method="POST" action="Ingredienteindex.php?modulo=detallePedidos&accion=crear">
    <label>ID Pedido:</label><input type="number" name="idPedido" required><br>
    <label>ID Producto:</label><input type="number" name="idProducto" required><br>
    <label>Cantidad:</label><input type="number" name="cantidadProducto" required><br>
    <label>Precio Unitario:</label><input type="text" name="precioUnitario" required><br>
    <label>Subtotal:</label><input type="text" name="subtotal" required><br>
    <button type="submit">Crear</button>
</form>

<hr>

<!-- Actualizar -->
<h2>Actualizar Detalle</h2>
<form method="POST" action="Ingredienteindex.php?modulo=detallePedidos&accion=actualizar">
    <label>ID Detalle:</label><input type="number" name="idDetalle" required><br>
    <label>ID Pedido:</label><input type="number" name="idPedido" required><br>
    <label>ID Producto:</label><input type="number" name="idProducto" required><br>
    <label>Cantidad:</label><input type="number" name="cantidadProducto" required><br>
    <label>Precio Unitario:</label><input type="text" name="precioUnitario" required><br>
    <label>Subtotal:</label><input type="text" name="subtotal" required><br>
    <button type="submit">Actualizar</button>
</form>

<hr>

<!-- Eliminar -->
<h2>Eliminar Detalle</h2>
<form method="POST" action="Ingredienteindex.php?modulo=detallePedidos&accion=eliminar">
    <label>ID Detalle:</label><input type="number" name="idDetalle" required><br>
    <button type="submit">Eliminar</button>
</form>
