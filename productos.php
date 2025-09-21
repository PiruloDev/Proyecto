<?php
$url_get = "http://localhost:8080/detalle/producto";
$url_post = "http://localhost:8080/crear/producto";
$url_actualizar = "http://localhost:8080/actualizar/producto";
$url_eliminar = "http://localhost:8080/eliminar/producto";
$consumo = file_get_contents($url_get);

if ($consumo === FALSE) {
    die("Error al consumir el servicio.");
}

$productos = json_decode($consumo, true);

echo "--------- PRODUCTOS ---------\n";

foreach ($productos as $producto) {
    echo "ID: " . $producto["Id Producto:"] . "\n";
    echo "Nombre: " . $producto["Nombre Producto:"] . "\n";
    echo "Precio: " . $producto["Precio:"] . "\n";
    echo "Stock Minímo: " . $producto["Stock Minímo:"] . "\n";
    echo "Marca Producto: " . $producto["Marca Producto:"] . "\n";
    echo "Fecha Vencimiento: " . $producto["Fecha Vencimiento:"] . "\n";
    echo "----------------------------------------------------------\n";
}

// Método POST
$respuesta = readline("¿Desea agregar un nuevo producto? escribe (s) para SI o escribe (n) para NO: ");

if ($respuesta === "s") {
    $nombre = readline("Ingrese nombre: ");
    $precio = readline("Ingrese precio: ");
    $stockMinimo = readline("Ingrese el stock minimo: ");
    $marcaProducto = readline("Ingrese la marca del producto: ");
    $fechaVencimiento = readline("Ingrese la fecha vencimiento del producto (YYYY-MM-DD): ");

    $datos = [
        "nombreProducto" => $nombre,
        "precio" => (float)$precio,
        "stockMinimo" => (int)$stockMinimo,
        "marcaProducto" => $marcaProducto,
        "fechaVencimiento" => $fechaVencimiento
    ];

    $data_json = json_encode($datos);

    $proceso = curl_init($url_post);
    curl_setopt($proceso, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($proceso, CURLOPT_POSTFIELDS, $data_json);
    curl_setopt($proceso, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($proceso, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_json)
    ));

    $respuestapet = curl_exec($proceso);

    $http_code = curl_getinfo($proceso, CURLINFO_HTTP_CODE);
    
    if (curl_errno($proceso)) {
        die("Error en la petición Post: " . curl_error($proceso) . "\n");
    }
    curl_close($proceso);

    if ($http_code === 200) {
        echo ("Producto guardado correctamente (200)\n");
    } else {
        echo ("Error en el servidor, respuesta $http_code\n");
    }
}

// Método Put

$respuesta_put = readline("¿Desea actualizar un producto? escribe (s) para SI o escribe (n) para NO: "); 

if ($respuesta_put === "s") {
    $id_actualizar = readline("Ingrese el ID del usuario a actualizar: ");
    $nombre = readline("Ingrese nombre: ");
    $precio = readline("Ingrese precio: ");
    $stockMinimo = readline("Ingrese el stock mínimo: ");
    $marcaProducto = readline("Ingrese la marca del producto: ");
    $fechaVencimiento = readline("Ingrese la fecha vencimiento del producto (YYYY-MM-DD): ");

    $datos_actualizar = array(
        "nombreProducto" => $nombre,
        "precio" => (float)$precio,
        "stockMinimo" => (int)$stockMinimo,
        "marcaProducto" => $marcaProducto,
        "fechaVencimiento" => $fechaVencimiento
    );

    $data_json_actualizar = json_encode($datos_actualizar);

    $url_actualizar = $url_actualizar . "/" . $id_actualizar;

    $proceso_put = curl_init($url_actualizar);

    curl_setopt($proceso_put, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($proceso_put, CURLOPT_POSTFIELDS, $data_json_actualizar);
    curl_setopt($proceso_put, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($proceso_put, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_json_actualizar)
    ));

    $respuestapet_put = curl_exec($proceso_put);

    $http_code_put = curl_getinfo($proceso_put, CURLINFO_HTTP_CODE);

    if (curl_errno($proceso_put)) {
        die("Error en la petición PUT: " . curl_error($proceso_put) . "\n");
    }

    curl_close($proceso_put);

    if ($http_code_put === 200) {
        echo "Usuario actualizado correctamente (200)\n";
        echo "Respuesta del servidor: $respuestapet_put\n";
    } else {
        echo "Error en el servidor. Código de respuesta: $http_code_put\n";
    }
}
// Método Delete
$respuesta_delete = readline("¿Desea eliminar un producto? escribe (s) para SI o escribe (n) para NO: ");
if ($respuesta_delete === "s") {
    $id_eliminar = readline("Ingrese el ID del producto a eliminar: ");

    $url_eliminar = $url_eliminar . "/" . $id_eliminar;

    $proceso_delete = curl_init($url_eliminar);

    curl_setopt($proceso_delete, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($proceso_delete, CURLOPT_RETURNTRANSFER, true);

    $respuestapet_delete = curl_exec($proceso_delete);

    $http_code_delete = curl_getinfo($proceso_delete, CURLINFO_HTTP_CODE);

    if (curl_errno($proceso_delete)) {
        die("Error en la petición DELETE: " . curl_error($proceso_delete) . "\n");
    }

    curl_close($proceso_delete);

    if ($http_code_delete === 200) {
        echo "Usuario eliminado correctamente (200)\n";
        echo "Respuesta del servidor: $respuestapet_delete\n";
    } else {
        echo "Error en el servidor. Código de respuesta: $http_code_delete\n";
    }
}
?>