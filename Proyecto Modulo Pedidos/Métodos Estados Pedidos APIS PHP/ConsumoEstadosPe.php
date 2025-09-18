<?php
require_once 'Config.php';
$url = API_BASE_URL . ENDPOINT_ESTADOS_PEDIDOS;

$consumo = file_get_contents($url);

if ($consumo === FALSE) {
    die("Error al consumir el servicio.");
}

$estadospedidos = json_decode($consumo);

if ($estadospedidos === null || empty($estadospedidos)) {
    die("No se pudieron decodificar los datos o no hay estados de pedidos.");
}

foreach ($estadospedidos as $estado) {
    echo "ID del Estado de Pedido: " . $estado->id_ESTADO_PEDIDO . ", Nombre del Estado: " . $estado->nombre_ESTADO . "\n";
}
?>