<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos - Usuario</title>
</head>
<body>
    <h1>Lista de Productos</h1>

    <?= $mensaje ?? '' ?>

    <?php if (is_array($productos)): ?>
        <ul>
            <?php foreach ($productos as $producto): ?>
                <li>
                    <?= htmlspecialchars($producto["nombreProducto"]) ?> |
                    Precio: <?= $producto["precio"] ?> |
                    Stock: <?= $producto["stockMinimo"] ?> |
                    Marca: <?= $producto["marcaProducto"] ?> |
                    Vence: <?= $producto["fechaVencimiento"] ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p style="color:red;">Error al obtener los productos.</p>
    <?php endif; ?>

    <h2>Agregar nuevo producto</h2>
    <form method="POST">
        <input type="hidden" name="accion" value="crear">
        Nombre: <input type="text" name="nombre" required><br>
        Precio: <input type="number" step="0.01" name="precio" required><br>
        Stock mínimo: <input type="number" name="stockMinimo" required><br>
        Marca: <input type="text" name="marca" required><br>
        Fecha vencimiento: <input type="date" name="fechaVencimiento" required><br>
        <input type="submit" value="Agregar Producto">
    </form>
</body>
</html>
