<?php

require_once 'Config.php';
$url = API_BASE_URL . ENDPOINT_PEDIDOS;

$consumo = file_get_contents($url);

if ($consumo === FALSE) {
    die("Error al consumir el servicio.");
}

$pedidos = json_decode($consumo);

if ($pedidos === null || empty($pedidos)) {
    die("No se pudieron decodificar los datos o no hay pedidos.");
}

foreach ($pedidos as $pedido) {
    echo "ID del Empleado: " . $pedido->id_EMPLEADO . ", ID del Cliente: " . $pedido->id_CLIENTE . ", Total: " . $pedido->total_PRODUCTO . "\n";
}
?>