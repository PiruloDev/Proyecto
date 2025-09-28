<?php
require_once "config.php";

// --------- MÉTODO GET ----------

$productos = consumirGET("/productos/mas-vendidos");

if ($consumo === FALSE) {
    die("Error al consumir el servicio.");
}

$productos = json_decode($consumo, true);

echo "--------- PRODUCTOS ---------\n";

foreach ($productos as $producto) {
    echo "ID: " . $producto["idProducto"] . "\n";
    echo "Nombre: " . $producto["nombreProducto"] . "\n";
    echo "Descripción: " . $producto["descripcionProducto"] . "\n";
    echo "Precio: " . $producto["precioProducto"] . "\n";
    echo "Stock Minímo: " . $producto["stockMin"] . "\n";
    echo "----------------------------------------------------------\n";
}
?>