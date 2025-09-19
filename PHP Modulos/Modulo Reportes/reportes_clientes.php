<?php
$url_get = "http://localhost:8080/clientes";      
$url_post = "http://localhost:8080/agregar/cliente"; 
$url_actualizar = "http://localhost:8080/actualizar/cliente";
$url_eliminar = "http://localhost:8080/eliminar/cliente";

// --------- MÉTODO GET ----------
$consumo = file_get_contents($url_get);
if ($consumo === FALSE) {
    die("Error al consumir el servicio. ");
}

$clientes = json_decode($consumo);

echo " Nombre \n";
echo "---------\n";

foreach ($clientes as $cliente) {
    echo $cliente . "\n";
}

// --------- MÉTODO POST ----------
$respuesta = readline ("¿Desea agregar un nuevo cliente? Coloca s para (Si) n para (No): ");
if ($respuesta === "s" ) {
    $nombre = readline ("Ingrese el nombre: ");

    $datos = array(
        "nombre" => $nombre
    );

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
        echo ("Cliente guardado correctamente (200)\n");
    } else {
        echo ("Error en el servidor, respuesta $http_code\n");
    }
}

// --------- MÉTODO PATCH ----------

$respuesta_patch = readline("¿Desea actualizar un usuario? escribe (s) para SI o escribe (n) para NO: "); 

if ($respuesta_patch === "s") {
    $id_actualizar = readline("Ingrese el ID del usuario a actualizar: ");
    $nombre = readline("Ingrese nuevo nombre: ");

    $datos_actualizar = array(
        "nombre" => $nombre
    );

    $data_json_actualizar = json_encode($datos_actualizar);

    $url_actualizar = $url_actualizar . "/" . $id_actualizar;

    $proceso_patch = curl_init($url_actualizar);

    curl_setopt($proceso_patch, CURLOPT_CUSTOMREQUEST, "PATCH");
    curl_setopt($proceso_patch, CURLOPT_POSTFIELDS, $data_json_actualizar);
    curl_setopt($proceso_patch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($proceso_patch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_json_actualizar)
    ));

    $respuestapet_patch = curl_exec($proceso_patch);

    $http_code_patch = curl_getinfo($proceso_patch, CURLINFO_HTTP_CODE);

    if (curl_errno($proceso_patch)) {
        die("Error en la petición PATCH: " . curl_error($proceso_patch) . "\n");
    }

    curl_close($proceso_patch);

    if ($http_code_patch === 200) {
        echo "Usuario actualizado correctamente (200)\n";
        echo "Respuesta del servidor: $respuestapet_patch\n";
    } else {
        echo "Error en el servidor. Código de respuesta: $http_code_patch\n";
    }
}

// --------- MÉTODO DELETE ----------

$respuesta_delete = readline("¿Desea eliminar un usuario? escribe (s) para SI o escribe (n) para NO: ");
if ($respuesta_delete === "s") {
    $id_eliminar = readline("Ingrese el ID del usuario a eliminar: ");

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