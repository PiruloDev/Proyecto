<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administradores</title>
</head>
<body>
    <h1>Gestión de Administradores</h1>

    <?= $mensaje ?? '' ?>

    <h2>Lista de administradores</h2>
    <?php if (!empty($administradores)): ?>
        <ul>
            <?php foreach ($administradores as $admin): ?>
                <li><?= htmlspecialchars($admin["nombre"] ?? "Sin nombre") ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p style="color:red;">No hay administradores registrados.</p>
    <?php endif; ?>

    <h2>Agregar nuevo administrador</h2>
    <form method="POST">
        <input type="hidden" name="accion" value="crear">
        Nombre: <input type="text" name="nombre" required><br>
        <input type="submit" value="Agregar">
    </form>

    <h2>Actualizar administrador</h2>
    <form method="POST">
        <input type="hidden" name="accion" value="actualizar">
        ID: <input type="number" name="id" required><br>
        Nombre: <input type="text" name="nombre" required><br>
        <input type="submit" value="Actualizar">
    </form>

    <h2>Eliminar administrador</h2>
    <form method="POST">
        <input type="hidden" name="accion" value="eliminar">
        ID: <input type="number" name="id" required><br>
        <input type="submit" value="Eliminar">
    </form>
</body>
</html>
