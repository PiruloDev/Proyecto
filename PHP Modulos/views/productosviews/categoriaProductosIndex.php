<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Categorías de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h1 class="mb-4 text-center">Categorías de Productos</h1>

    <!-- Formulario para crear nueva categoría -->
    <form method="POST" action="index.php?action=crear" class="mb-4">
        <div class="input-group">
            <input type="text" name="nombre" class="form-control" placeholder="Nombre de la categoría" required>
            <button type="submit" class="btn btn-success">Agregar</button>
        </div>
    </form>

    <!-- Tabla de categorías -->
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre Categoría</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $categorias->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?= $row['ID_CATEGORIA_PRODUCTO'] ?></td>
                <td><?= $row['NOMBRE_CATEGORIAPRODUCTO'] ?></td>
                <td>
                    <!-- Formulario para actualizar -->
                    <form method="POST" action="index.php?action=actualizar" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $row['ID_CATEGORIA_PRODUCTO'] ?>">
                        <input type="text" name="nombre" value="<?= $row['NOMBRE_CATEGORIAPRODUCTO'] ?>" required>
                        <button type="submit" class="btn btn-primary btn-sm">Actualizar</button>
                    </form>
                    <!-- Botón para eliminar -->
                    <a href="index.php?action=eliminar&id=<?= $row['ID_CATEGORIA_PRODUCTO'] ?>" class="btn btn-danger btn-sm">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
