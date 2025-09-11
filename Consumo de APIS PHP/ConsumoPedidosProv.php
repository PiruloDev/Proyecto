<?php

$url = "http://localhost:8080/pedidosproveedores";

$consumo = file_get_contents($url);

if ($consumo === FALSE) {
    die("Error al consumir el servicio de pedidos de proveedores. Asegúrate de que el servicio de Spring Boot esté en ejecución.");
}

$pedidosProveedores = json_decode($consumo);

if ($pedidosProveedores === null || empty($pedidosProveedores)) {
    die("No se pudieron decodificar los datos o no hay pedidos de proveedores.");
}

foreach ($pedidosProveedores as $pedido) {
   
    echo "ID del Pedido: " . $pedido->id_PEDIDO_PROV . ", ID del Proveedor: " . $pedido->id_PROVEEDOR . ", Número de Pedido: " . $pedido->numero_PEDIDO . "\n";
}

?>