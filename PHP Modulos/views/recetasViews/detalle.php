<h2>Detalle de Receta</h2>

<?php if (!empty($receta)): ?>
    <p><strong>ID Producto:</strong> <?= $receta['idProducto'] ?></p>

    <h3>Ingredientes</h3>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>ID Ingrediente</th>
                <th>Cantidad Necesaria</th>
                <th>Unidad</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($receta['ingredientes'] as $ing): ?>
                <tr>
                    <td><?= $ing['idIngrediente'] ?></td>
                    <td><?= $ing['cantidadNecesaria'] ?></td>
                    <td><?= $ing['unidadMedida'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <a href="?accion=editar&id=<?= $receta['idProducto'] ?>">✏️ Editar Receta</a> |
    <a href="?accion=listar">🔙 Volver al listado</a>
<?php else: ?>
    <p>No se encontró la receta solicitada.</p>
    <a href="?accion=listar">Volver</a>
<?php endif; ?>
