<?php
$url = "http://localhost:8080/pedidos";
$consumo = file_get_contents($url);

if ($consumo === FALSE) {
    die("Error al consumir el servicio.");
}

$pedidos = json_decode($consumo);

if ($pedidos === null || empty($pedidos)) {
    die("No se pudieron decodificar los datos o no hay pedidos.");
}

foreach ($pedidos as $pedido) {
    echo "ID del Empleado: " . $pedido->id_EMPLEADO .
         ", ID del Cliente: " . $pedido->id_CLIENTE .
         ", Total: " . $pedido->total_PRODUCTO . "\n";
}

// MÉTODO DELETE
$respuesta_delete = readline("¿Desea eliminar un pedido? Coloca s para(si) n para (NO) ");

if ($respuesta_delete === "s") {
    $ID_PEDIDO = readline("Ingrese el ID del pedido a eliminar: ");
    
    // Construir la URL completa para el recurso específico (ej. http://localhost:8080/pedidos/123)
    $url_delete = $url . "/" . $ID_PEDIDO;

    $proceso_delete = curl_init($url_delete);

    // Configuración para el método DELETE
    curl_setopt($proceso_delete, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($proceso_delete, CURLOPT_RETURNTRANSFER, true);

    $respuestapet_delete = curl_exec($proceso_delete);
    $http_code_delete = curl_getinfo($proceso_delete, CURLINFO_HTTP_CODE);

    if (curl_errno($proceso_delete)) {
        die("Error en la petición DELETE: " . curl_error($proceso_delete));
    }

    curl_close($proceso_delete);

    if ($http_code_delete == 200 || $http_code_delete == 204) {
        echo "Pedido con ID $ID_PEDIDO eliminado correctamente. Respuesta HTTP: $http_code_delete\n";
        echo "Respuesta del servidor: $respuestapet_delete\n";
    } else {
        echo "Error al intentar eliminar el pedido. Código de respuesta: $http_code_delete\n";
        echo "Respuesta del servidor: $respuestapet_delete\n";
    }
}
?>
