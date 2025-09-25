<?php
require_once 'Config.php';
$url = API_BASE_URL . ENDPOINT_PUT_PEDIDOS;
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

// MÉTODO PUT
$respuesta_put = readline("¿Desea actualizar un pedido? Coloca s para(si) n para (NO) ");

if ($respuesta_put === "s") {
    $ID_PEDIDO = readline("Ingrese el ID del pedido a actualizar: ");
    $ID_CLIENTE = readline("Ingrese el nuevo ID del cliente: ");
    $ID_EMPLEADO = readline("Ingrese el nuevo ID del empleado: ");
    $ID_ESTADO_PEDIDO = readline("Ingrese el nuevo ID del estado: ");
    $total_PRODUCTO = readline("Ingrese el nuevo valor total del pedido: ");
    $fechaIngreso = date("c"); 
    $fechaEntrega = date("c");   
    
    // Construir la URL completa para el recurso específico (ej. http://localhost:8080/pedidos/123)
    $url_put = $url . "/" . $ID_PEDIDO;

    $datos_put = array(
        "id_CLIENTE" => (int)$ID_CLIENTE,
        "id_EMPLEADO" => (int)$ID_EMPLEADO,
        "id_ESTADO_PEDIDO" => (int)$ID_ESTADO_PEDIDO,
        "total_PRODUCTO" => (float)$total_PRODUCTO,
        "fecha_INGRESO" => $fechaIngreso,
        "fecha_ENTREGA" => $fechaEntrega
    );

    $data_json_put = json_encode($datos_put);

    if ($data_json_put === false) {
        die("Error al codificar los datos a JSON: " . json_last_error_msg());
    }

    $proceso_put = curl_init($url_put);

    // Configuración para el método PUT
    curl_setopt($proceso_put, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($proceso_put, CURLOPT_POSTFIELDS, $data_json_put);
    curl_setopt($proceso_put, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($proceso_put, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_json_put)
    ));

    $respuestapet_put = curl_exec($proceso_put);
    $http_code_put = curl_getinfo($proceso_put, CURLINFO_HTTP_CODE);

    if (curl_errno($proceso_put)) {
        die("Error en la petición PUT: " . curl_error($proceso_put));
    }

    curl_close($proceso_put);

    if ($http_code_put >= 200 && $http_code_put < 300) {
        echo "Pedido actualizado correctamente. Respuesta HTTP: $http_code_put\n";
        echo "Respuesta del servidor: $respuestapet_put\n";
    } else {
        echo "Error en el servidor. Código de respuesta: $http_code_put\n";
        echo "Respuesta del servidor: $respuestapet_put\n";
    }
}
?>
