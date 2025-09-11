<?php

$url = "http://localhost:8080/estadosPedidos";

$idBuscado = readline(prompt: "Ingrese el ID del estado de pedido que desea buscar (1-5): ");

$consumo = file_get_contents($url);

if ($consumo === FALSE) {
    die("Error al consumir el servicio. Asegúrate de que el servicio de Spring Boot esté en ejecución.\n");
}

$estados = json_decode($consumo);

if ($estados === null || empty($estados)) {
    die("No se pudieron decodificar los datos o no hay estados de pedidos.\n");
}

$estadoEncontrado = null;

foreach ($estados as $estado) {
   
    if ($estado->id_ESTADO_PEDIDO == $idBuscado) {
        $estadoEncontrado = $estado;
        break; 
    }
}

if ($estadoEncontrado) {
    echo "ID del Estado: " . $estadoEncontrado->id_ESTADO_PEDIDO . "\n";
    echo "Nombre del Estado: " . $estadoEncontrado->nombre_ESTADO . "\n";
} else {
    echo "No se encontró un estado con el ID: " . $idBuscado . ".\n";
}

?>