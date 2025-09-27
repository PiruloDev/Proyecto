<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin - Productos</title>
</head>
<body>
    <h1>Gestión de Productos (Administrador)</h1>

    <?= $mensaje ?? '' ?>

    <h2>Lista de productos</h2>
    <ul>
        <?php foreach ($productos as $p): ?>
            <li>
                <?= $p["nombreProducto"] ?> |
                Precio: <?= $p["precio"] ?> |
                Stock: <?= $p["stockMinimo"] ?> |
                Marca: <?= $p["marcaProducto"] ?> |
                Vence: <?= $p["fechaVencimiento"] ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <h2>Agregar producto</h2>
    <form method="POST">
        <input type="hidden" name="accion" value="crear">
        Nombre: <input type="text" name="nombre" required><br>
        Precio: <input type="number" step="0.01" name="precio" required><br>
        Stock: <input type="number" name="stockMinimo" required><br>
        Marca: <input type="text" name="marca" required><br>
        Fecha vencimiento: <input type="date" name="fechaVencimiento" required><br>
        <input type="submit" value="Agregar">
    </form>

    <h2>Actualizar producto</h2>
    <form method="POST">
        <input type="hidden" name="accion" value="actualizar">
        ID: <input type="number" name="id" required><br>
        Nombre: <input type="text" name="nombre" required><br>
        Precio: <input type="number" step="0.01" name="precio" required><br>
        Stock: <input type="number" name="stockMinimo" required><br>
        Marca: <input type="text" name="marca" required><br>
        Fecha vencimiento: <input type="date" name="fechaVencimiento" required><br>
        <input type="submit" value="Actualizar">
    </form>

    <h2>Eliminar producto</h2>
    <form method="POST">
        <input type="hidden" name="accion" value="eliminar">
        ID: <input type="number" name="id" required><br>
        <input type="submit" value="Eliminar">
    </form>
</body>
</html>
