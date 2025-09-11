<?php

$url = "http://localhost:8080/pedidos";

echo "Autenticación de Usuario\n";
$usuarioRegistrado = readline(prompt: "¿Está usted registrado? (1 para sí, 0 para no): ");

if ($usuarioRegistrado != 1) {
    die("Acceso denegado. Debe ser un usuario registrado para ver los pedidos.\n");
}

echo "Acceso autorizado.\n";

$idBuscado = readline(prompt: "Ingrese el ID del pedido que desea buscar: ");

$consumo = file_get_contents($url);

if ($consumo === FALSE) {
    die("Error al consumir el servicio.\n");
}

$pedidos = json_decode($consumo);

if ($pedidos === null || empty($pedidos)) {
    die("No se pudieron decodificar los datos o no hay pedidos.");
}

$pedidoEncontrado = null;

foreach ($pedidos as $pedido) {
    if ($pedido->id_PEDIDO == $idBuscado) {
        $pedidoEncontrado = $pedido;
        break;
    }
}

if ($pedidoEncontrado) {
    echo "\nDetalles del Pedido Encontrado:\n";
    echo "ID del Pedido: " . $pedidoEncontrado->id_PEDIDO . "\n";
    echo "ID del Empleado: " . $pedidoEncontrado->id_EMPLEADO . "\n";
    echo "ID del Cliente: " . $pedidoEncontrado->id_CLIENTE . "\n";
    echo "Total del Producto: " . $pedidoEncontrado->total_PRODUCTO . "\n";
    echo "\n";
} else {
    echo "\nLo sentimos, no se encontró un pedido con el ID " . $idBuscado . ".\n";
    echo "\n";
}
?>