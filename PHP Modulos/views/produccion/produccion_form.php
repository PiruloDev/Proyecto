<h2>Gestión de Producción</h2>

<!-- Formulario para consultar receta -->
<form method="GET" action="">
    <label for="idProducto">ID Producto:</label>
    <input type="number" name="idProducto" required>
    <button type="submit" name="accion" value="verReceta">Ver Receta</button>
</form>

<hr>

<?php if (isset($receta) && is_array($receta) && count($receta) > 0): ?>
    <h3>Receta del producto <?= htmlspecialchars($idProducto) ?>:</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID Ingrediente</th>
            <th>Cantidad Requerida</th>
            <th>Unidad de Medida</th>
        </tr>
        <?php foreach ($receta as $ingrediente): ?>
            <tr>
                <td><?= htmlspecialchars($ingrediente['idIngrediente']) ?></td>
                <td><?= htmlspecialchars($ingrediente['cantidadRequerida']) ?></td>
                <td><?= htmlspecialchars($ingrediente['unidadMedida']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <hr>

    <!-- POST: Registrar producción -->
    <h3>Registrar Producción</h3>
    <form method="POST" action="?modulo=produccion&accion=registrar">
        <input type="hidden" name="idProducto" value="<?= htmlspecialchars($idProducto) ?>">
        <label for="cantidad">Cantidad a producir:</label>
        <input type="number" name="cantidad" required><br><br>

        <?php foreach ($receta as $ingrediente): ?>
            <input type="hidden" 
                   name="ingredientesDescontados[<?= $ingrediente['idIngrediente'] ?>]" 
                   value="<?= htmlspecialchars($ingrediente['cantidadRequerida']) ?>">
        <?php endforeach; ?>

        <button type="submit">Registrar Producción</button>
    </form>
<?php elseif (isset($idProducto)): ?>
    <p style="color:red;">No se encontró receta para el producto <?= htmlspecialchars($idProducto) ?>.</p>
<?php endif; ?>

<hr>

<!-- PUT: Actualizar producción completa -->
<h3>Actualizar Producción (PUT)</h3>
<form method="POST" action="?modulo=produccion&accion=actualizar">
    <input type="hidden" name="_method" value="PUT">
    <label for="idProduccion">ID Producción:</label>
    <input type="number" name="idProduccion" required><br><br>

    <label for="idProducto">Nuevo ID Producto:</label>
    <input type="number" name="idProducto" required><br><br>

    <label for="cantidad">Nueva Cantidad:</label>
    <input type="number" name="cantidad" required><br><br>

    <button type="submit">Actualizar Producción</button>
</form>

<hr>

<!-- PATCH: Actualizar parcialmente -->
<h3>Actualizar Parcialmente Producción (PATCH)</h3>
<form method="POST" action="?modulo=produccion&accion=parcial">
    <input type="hidden" name="_method" value="PATCH">
    <label for="idProduccion">ID Producción:</label>
    <input type="number" name="idProduccion" required><br><br>

    <label for="cantidad">Nueva Cantidad (opcional):</label>
    <input type="number" name="cantidad"><br><br>

    <button type="submit">Actualizar Parcial</button>
</form>

<hr>

<!-- DELETE: Eliminar producción -->
<h3>Eliminar Producción</h3>
<form method="POST" action="?modulo=produccion&accion=eliminar">
    <input type="hidden" name="_method" value="DELETE">
    <label for="idProduccion">ID Producción:</label>
    <input type="number" name="idProduccion" required><br><br>

    <button type="submit" style="color:red;">Eliminar</button>
</form>

<hr>

<?php if (isset($mensaje)): ?>
    <h3>Resultado:</h3>
    <p><?= htmlspecialchars($mensaje) ?></p>
<?php endif; ?>
