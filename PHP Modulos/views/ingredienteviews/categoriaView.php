<!DOCTYPE html>
<html>
<head>
    <title>Categorías de Ingredientes</title>
</head>
<body>
    <h1>Gestión de Categorías de Ingredientes</h1>

    <?php if (!empty($mensaje)) : ?>
        <p><strong>Respuesta API:</strong> <?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <h2>Lista de Categorías</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
        </tr>
        <?php if (!empty($categorias)) : ?>
            <?php foreach ($categorias as $c) : ?>
                <tr>
                    <td><?= htmlspecialchars($c['idCategoriaIngrediente']) ?></td>
                    <td><?= htmlspecialchars($c['nombreCategoria']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="2">No hay categorías.</td></tr>
        <?php endif; ?>
    </table>

    <h2>Agregar Categoría</h2>
    <form method="POST" action="?modulo=categoria&accion=agregar">
        <input type="text" name="nombreCategoria" placeholder="Nombre categoría" required>
        <button type="submit">Agregar</button>
    </form>

    <h2>Editar Categoría</h2>
    <form method="POST" action="?modulo=categoria&accion=editar">
        <input type="number" name="idCategoria" placeholder="ID categoría" required>
        <input type="text" name="nombreCategoria" placeholder="Nuevo nombre" required>
        <button type="submit">Editar</button>
    </form>

    <h2>Eliminar Categoría</h2>
    <form method="POST" action="?modulo=categoria&accion=eliminar">
        <input type="number" name="idCategoria" placeholder="ID categoría" required>
        <button type="submit">Eliminar</button>
    </form>
</body>
</html>
