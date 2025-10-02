<h2>Listado de Recetas</h2>
<a href="?accion=crear">➕ Nueva Receta</a>

<table border="1" cellpadding="8">
    <thead>
        <tr>
            <th>ID Receta</th>
            <th>Producto</th>
            <th>Ingredientes</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($recetas as $receta): ?>
            <tr>
                <td><?= $receta['id'] ?></td>
                <td><?= $receta['producto'] ?></td>
                <td>
                    <?php foreach ($receta['ingredientes'] as $ing): ?>
                        <?= $ing['idIngrediente'] ?> - 
                        <?= $ing['cantidadNecesaria'] . " " . $ing['unidadMedida'] ?><br>
                    <?php endforeach; ?>
                </td>
                <td>
                    <a href="?accion=detalle&id=<?= $receta['id'] ?>">Ver</a> |
                    <a href="?accion=editar&id=<?= $receta['id'] ?>">Editar</a> |
                    <a href="?accion=eliminar&id=<?= $receta['id'] ?>">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
