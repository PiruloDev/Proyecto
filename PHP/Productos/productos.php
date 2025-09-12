<?php
$url = "http://localhost:8080/detalle/producto";  
$consumo = file_get_contents($url);

if ($consumo === FALSE) {
    die("Error al consumir el servicio.");
}

$productos = json_decode($consumo);

echo "--------- PRODUCTOS ---------\n";

foreach ($productos as $producto) {
    echo "ID: " . $producto->id . "\n";
    echo "Nombre: " . $producto->nombre . "\n";
    echo "Precio: " . $producto->precio . "\n";
    echo "Activo: " . ($producto->activo == 1 ? "Sí" : "No") . "\n";
    echo "-----------------------------\n";
}
?>
























