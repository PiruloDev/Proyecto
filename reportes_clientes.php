<?php
$url_get = "http://localhost:8080/clientes";      
$url_post = "http://localhost:8080/agregar/cliente"; 

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
?>