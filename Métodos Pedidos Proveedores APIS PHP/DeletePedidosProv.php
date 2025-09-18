<?php

$url = "http://localhost:8080/pedidosproveedores";

$consumo = file_get_contents($url);

if ($consumo === FALSE) {
    die("Error al consumir el servicio de pedidos de proveedores.");
}

$pedidosProveedores = json_decode($consumo);

if ($pedidosProveedores === null || empty($pedidosProveedores)) {
    die("No se pudieron decodificar los datos o no hay pedidos de proveedores.");
}

foreach ($pedidosProveedores as $pedido) {
    echo "ID del Pedido: " . $pedido->id_PEDIDO_PROV . 
         ", ID del Proveedor: " . $pedido->id_PROVEEDOR . 
         ", Número de Pedido: " . $pedido->numero_PEDIDO . 
         ", Fecha del Pedido: " . $pedido->fecha_PEDIDO . 
         ", Estado del Pedido: " . $pedido->estado_PEDIDO . "\n";
}
// MÉTODO DELETE 
$respuesta = readline("¿Desea eliminar un pedido a proveedor? Coloque d para (si) o n para (no): ");

if ($respuesta === "d") {
    $id_pedido_prov = readline("Ingrese el ID del pedido a proveedor que desea eliminar: ");

    $url_delete = $url . "/" . $id_pedido_prov;

    $proceso = curl_init($url_delete);

    curl_setopt($proceso, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($proceso, CURLOPT_RETURNTRANSFER, true);

    $respuesta_peticion = curl_exec($proceso);
    $http_code = curl_getinfo($proceso, CURLINFO_HTTP_CODE);

    if (curl_errno($proceso)) {
        die("Error en la petición DELETE: " . curl_error($proceso));
    }

    curl_close($proceso);

    if ($http_code == 200 || $http_code == 204) {
        echo "Pedido a proveedor con ID " . $id_pedido_prov . " eliminado correctamente. Respuesta HTTP: $http_code\n";
    } else {
        echo "Error en el servidor. Código de respuesta: $http_code\n";
        echo "Respuesta del servidor: $respuesta_peticion\n";
    }
}
?>