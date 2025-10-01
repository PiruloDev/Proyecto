<!-- views/produccion/produccion_view.php -->

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

    <!-- Formulario para registrar producción -->
    <form method="POST" action="?modulo=produccion&accion=registrar">
        <input type="hidden" name="idProducto" value="<?= htmlspecialchars($idProducto) ?>">

        <label for="cantidad">Cantidad a producir:</label>
        <input type="number" name="cantidad" required><br><br>

        <button type="submit">Registrar Producción</button>
    </form>
<?php elseif (isset($idProducto) && empty($receta)): ?>
    <p style="color:red;">No se encontró receta para el producto <?= htmlspecialchars($idProducto) ?>.</p>
<?php endif; ?>

<hr>

<?php if (isset($mensaje)): ?>
    <h3>Resultado:</h3>
    <p><?= htmlspecialchars($mensaje) ?></p>
<?php endif; ?>
