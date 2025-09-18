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

//MÉTODO POST 
$respuesta = readline("¿Desea agregar un nuevo pedido? Coloca s para(si) n para (NO) ");

if ($respuesta === "s") {

    $ID_CLIENTE = readline("Ingrese el ID del cliente: ");
    $ID_EMPLEADO = readline("Ingrese el ID del empleado: ");
    $ID_ESTADO_PEDIDO= readline("Ingrese el ID del estado: ");
    $total_PRODUCTO  = readline("Ingrese el valor total del pedido: ");
    $fechaIngreso = date("c"); 
    $fechaEntrega = date("c");  

    $datos = array(
        "id_CLIENTE"      => (int)$ID_CLIENTE,
        "id_EMPLEADO"     => (int)$ID_EMPLEADO,
        "id_ESTADO_PEDIDO"=> (int)$ID_ESTADO_PEDIDO,
        "total_PRODUCTO"  => (float)$total_PRODUCTO,
        "fecha_INGRESO"   => $fechaIngreso,
        "fecha_ENTREGA"   => $fechaEntrega
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

    $respuestapet = curl_exec($proceso);
    $http_code    = curl_getinfo($proceso, CURLINFO_HTTP_CODE);

    if (curl_errno($proceso)) {
        die("Error en la petición POST: " . curl_error($proceso));
    }

    curl_close($proceso);

    if ($http_code >= 200 && $http_code < 300) {
        echo "Pedido guardado correctamente. Respuesta HTTP: $http_code\n";
        echo "Respuesta del servidor: $respuestapet\n";
    } else {
        echo "Error en el servidor. Código de respuesta: $http_code\n";
        echo "Respuesta del servidor: $respuestapet\n";
    }
}
?>
