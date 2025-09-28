<h1>Gestión de Detalle de Pedidos</h1>

---

<h2>Lista de Detalles</h2>
<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th>ID Detalle</th>
            <th>ID Pedido</th>
            <th>ID Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($detalles)): ?>
            <?php foreach ($detalles as $d): ?>
                <tr>
                    <td><?= htmlspecialchars($d['idDetalle'] ?? '') ?></td>
                    <td><?= htmlspecialchars($d['idPedido'] ?? '') ?></td>
                    <td><?= htmlspecialchars($d['idProducto'] ?? '') ?></td>
                    <td><?= htmlspecialchars($d['cantidadProducto'] ?? '') ?></td>
                    <td><?= htmlspecialchars($d['precioUnitario'] ?? '') ?></td>
                    <td><?= htmlspecialchars($d['subtotal'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6">No hay registros de detalles de pedidos.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

---

<h2>Crear Detalle</h2>
<form method="POST" action="Ingredienteindex.php?modulo=detallePedidos&accion=crear">
    <label for="idPedido_crear">ID Pedido:</label>
    <input type="number" id="idPedido_crear" name="idPedido" required><br>
    
    <label for="idProducto_crear">ID Producto:</label>
    <input type="number" id="idProducto_crear" name="idProducto" required><br>
    
    <label for="cantidadProducto_crear">Cantidad:</label>
    <input type="number" id="cantidadProducto_crear" name="cantidadProducto" required><br>
    
    <label for="precioUnitario_crear">Precio Unitario:</label>
    <input type="text" id="precioUnitario_crear" name="precioUnitario" required><br>
    
    <label for="subtotal_crear">Subtotal:</label>
    <input type="text" id="subtotal_crear" name="subtotal" required><br>
    
    <button type="submit">Crear Detalle</button>
</form>

---

<h2>Actualizar Detalle</h2>
<form method="POST" action="Ingredienteindex.php?modulo=detallePedidos&accion=actualizar">
    <label for="idDetalle_actualizar">ID Detalle a Actualizar:</label>
    <input type="number" id="idDetalle_actualizar" name="idDetalle" required><br>
    
    <label for="idPedido_actualizar">ID Pedido:</label>
    <input type="number" id="idPedido_actualizar" name="idPedido" required><br>
    
    <label for="idProducto_actualizar">ID Producto:</label>
    <input type="number" id="idProducto_actualizar" name="idProducto" required><br>
    
    <label for="cantidadProducto_actualizar">Nueva Cantidad:</label>
    <input type="number" id="cantidadProducto_actualizar" name="cantidadProducto" required><br>
    
    <label for="precioUnitario_actualizar">Nuevo Precio Unitario:</label>
    <input type="text" id="precioUnitario_actualizar" name="precioUnitario" required><br>
    
    <label for="subtotal_actualizar">Nuevo Subtotal:</label>
    <input type="text" id="subtotal_actualizar" name="subtotal" required><br>
    
    <button type="submit">Actualizar Detalle</button>
</form>

---

<h2>Eliminar Detalle</h2>
<form method="POST" action="Ingredienteindex.php?modulo=detallePedidos&accion=eliminar">
    <label for="idDetalle_eliminar">ID Detalle a Eliminar:</label>
    <input type="number" id="idDetalle_eliminar" name="idDetalle" required><br>
    
    <button type="submit">Eliminar Detalle</button>
</form>