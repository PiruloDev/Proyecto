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

// MÉTODO DELETE 
$respuesta = readline("¿Desea eliminar un estado de pedido? Coloque s para (si) o n para (no): ");

if ($respuesta === "s") {
    $id_estado = readline("Ingrese el ID del estado que desea eliminar: ");

    $url_delete = $url . "/" . $id_estado;

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
        echo "Estado de pedido con ID " . $id_estado . " eliminado correctamente. Respuesta HTTP: $http_code\n";
    } else {
        echo "Error en el servidor. Código de respuesta: $http_code\n";
        echo "Respuesta del servidor: $respuesta_peticion\n";
    }
}
?>