<?php
$url = "http://localhost:8080/estadosPedidos";
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

// MÉTODO POST 
$respuesta = readline("¿Desea agregar un nuevo estado de pedido? Coloque s para (si) o n para (no): ");

if ($respuesta === "s") {
    $nombreEstado = readline("Ingrese el nombre del nuevo estado de pedido: ");

    $datos = array(
        "nombre_ESTADO" => $nombreEstado
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
        echo "Estado de pedido guardado correctamente. Respuesta HTTP: $http_code\n";
        echo "Respuesta del servidor: $respuesta_peticion\n";
    } else {
        echo "Error en el servidor. Código de respuesta: $http_code\n";
        echo "Respuesta del servidor: $respuesta_peticion\n";
    }
}
?>