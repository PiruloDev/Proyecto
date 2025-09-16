<?php
$url = "http://localhost:8080/detalle/producto";
$consumo = file_get_contents($url);

if ($consumo === FALSE) {
    die("Error al consumir el servicio.");
}

$productos = json_decode($consumo, true);

echo "--------- PRODUCTOS ---------\n";

foreach ($productos as $producto) {
    echo "ID: " . $producto["Id:"] . "\n";
    echo "Nombre: " . $producto["Nombre:"] . "\n";
    echo "Precio: " . $producto["Precio:"] . "\n";
    echo "-----------------------------\n";
}

$respuesta = readline("¿Desea agregar un nuevo producto? (s para si o n para no): ");

if ($respuesta === "s") {
    $nombre = readline("Ingrese nombre: ");
    $precio = readline("Ingrese precio: ");

    $datos = array(
        "Nombre" => $nombre,
        "Precio" => (int)$precio
    );

    $data_json = json_encode($datos);

    $proceso = curl_init($url);

    curl_setopt($proceso, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($proceso, CURLOPT_POSTFIELDS, $data_json);
    curl_setopt($proceso, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($proceso, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_json)
    ));

    $respuestaPost = curl_exec($proceso);
    $http_code = curl_getinfo($proceso, CURLINFO_HTTP_CODE);

    if (curl_errno($proceso)) {
        die("Error en la petición POST: " . curl_error($proceso) . "\n");
    }

    curl_close($proceso);

    if ($http_code === 200) {
        echo "Producto guardado correctamente. Respuesta: (200)\n";
    } else {
        echo "Error en el servidor. Código de respuesta: $http_code\n";
    }
}
?>
