<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuario - Productos</title>
</head>
<body>
    <h1>Productos disponibles</h1>

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
</body>
</html>
