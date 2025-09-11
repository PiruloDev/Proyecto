<?php

$url = "http://localhost:8080/estadosPedidos";

$consumo = file_get_contents($url);

if ($consumo === FALSE) {
    die("Error al consumir el servicio de estados. Asegúrate de que el servicio de Spring Boot esté en ejecución.");
}

$estados = json_decode($consumo);

if ($estados === null || empty($estados)) {
    die("No se pudieron decodificar los datos o no hay estados de pedidos.");
}

foreach ($estados as $estado) {
   
    echo "ID del Estado: " . $estado->id_ESTADO_PEDIDO . ", Estado: " . $estado->nombre_ESTADO . "\n";
}

?>