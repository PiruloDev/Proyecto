<?php

require_once 'Config.php';
$url = API_BASE_URL . ENDPOINT_PEDIDOS_PROVEEDORES;

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

// MÉTODO POST 
$respuesta = readline("¿Desea agregar un nuevo pedido a proveedor? Coloque s para (si) o n para (no): ");

if ($respuesta === "s") {
    $id_proveedor = readline("Ingrese el ID del proveedor: ");
    $numero_pedido = readline("Ingrese el número de pedido: ");
    $fecha_pedido = date("c"); 
    $estado_pedido = readline("Ingrese el estado del pedido: ");

    $datos = array(
        "id_PROVEEDOR"  => (int)$id_proveedor,
        "numero_PEDIDO" => (int)$numero_pedido,
        "fecha_PEDIDO"  => $fecha_pedido,
        "estado_PEDIDO" => $estado_pedido
    );

    $data_json = json_encode($datos);

    if ($data_json === false) {
        die("Error al codificar los datos a JSON: " . json_last_error_msg());
    }

    $proceso = curl_init($url);

    curl_setopt($proceso, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($proceso, CURLOPT_POSTFIELDS, $data_json);
    curl_setopt($proceso, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($proceso, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_json)
    ));

    $respuesta_peticion = curl_exec($proceso);
    $http_code = curl_getinfo($proceso, CURLINFO_HTTP_CODE);

    if (curl_errno($proceso)) {
        die("Error en la petición POST: " . curl_error($proceso));
    }

    curl_close($proceso);

    if ($http_code >= 200 && $http_code < 300) {
        echo "Pedido a proveedor guardado correctamente. Respuesta HTTP: $http_code\n";
        echo "Respuesta del servidor: $respuesta_peticion\n";
    } else {
        echo "Error en el servidor. Código de respuesta: $http_code\n";
        echo "Respuesta del servidor: $respuesta_peticion\n";
    }
}

?>