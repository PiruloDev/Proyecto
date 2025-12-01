<?php
$pdo = new PDO('mysql:host=localhost;dbname=proyectopanaderia;charset=utf8', 'root', '');
$stmt = $pdo->prepare('SELECT p.NOMBRE_PRODUCTO, cp.NOMBRE_CATEGORIAPRODUCTO FROM Productos p INNER JOIN Categoria_Productos cp ON p.ID_CATEGORIA_PRODUCTO = cp.ID_CATEGORIA_PRODUCTO WHERE p.ACTIVO = 1 ORDER BY cp.NOMBRE_CATEGORIAPRODUCTO, p.NOMBRE_PRODUCTO');
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($productos as $producto) {
    echo $producto['NOMBRE_CATEGORIAPRODUCTO'] . ' - ' . $producto['NOMBRE_PRODUCTO'] . PHP_EOL;
}
?>
