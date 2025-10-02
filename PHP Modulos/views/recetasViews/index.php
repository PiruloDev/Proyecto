<!-- views/recetasViews/index.php -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Recetas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
        a, button { margin: 2px; text-decoration: none; }
        .btn { padding: 6px 10px; border-radius: 5px; font-size: 14px; border: none; cursor: pointer; }
        .btn-detalle { background: #3498db; color: #fff; }
        .btn-editar { background: #f39c12; color: #fff; }
        .btn-eliminar { background: #e74c3c; color: #fff; }
        .btn-crear { background: #2ecc71; color: #fff; margin-top: 10px; display: inline-block; }
    </style>
</head>
<body>

    <h1>📋 Gestión de Recetas</h1>

    <?php if (isset($_GET['msg'])): ?>
        <p style="color: green;"><?= htmlspecialchars($_GET['msg']) ?></p>
    <?php endif; ?>

    <a href="?accion=crear" class="btn btn-crear">➕ Crear nueva receta</a>

    <table>
        <thead>
            <tr>
                <th>ID Producto</th>
                <th>Ingredientes</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($recetas)): ?>
            <?php foreach ($recetas as $receta): ?>
                <tr>
                    <td><?= htmlspecialchars($receta['idProducto']) ?></td>
                    <td>
                        <ul>
                            <?php if (!empty($receta['ingredientes']) && is_array($receta['ingredientes'])): ?>
                                <?php foreach ($receta['ingredientes'] as $ing): ?>
                                    <li>
                                        <?= htmlspecialchars($ing['idIngrediente']) ?> - 
                                        <?= htmlspecialchars($ing['cantidadNecesaria']) ?> 
                                        <?= htmlspecialchars($ing['unidadMedida']) ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>Sin ingredientes registrados</li>
                            <?php endif; ?>
                        </ul>
                    </td>
                    <td>
                        <a href="?accion=detalle&id=<?= $receta['idProducto'] ?>" class="btn btn-detalle">👁 Ver</a>
                        <a href="?accion=editar&id=<?= $receta['idProducto'] ?>" class="btn btn-editar">✏ Editar</a>
                        <a href="?accion=eliminar&id=<?= $receta['idProducto'] ?>" class="btn btn-eliminar"
                           onclick="return confirm('¿Seguro que deseas eliminar esta receta?');">🗑 Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3">No hay recetas registradas.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
