<?php

$BASE_URL = "http://localhost:8080";

// Función para consumir servicios con GET
function consumirGET($endpoint) {
    global $BASE_URL;
    $url = $BASE_URL . $endpoint;

    $respuesta = file_get_contents($url);
    if ($respuesta === FALSE) {
        die("Error al consumir el servicio: $url");
    }
    return json_decode($respuesta);
}

// Función para enviar datos con cURL (POST, PATCH, DELETE)
function consumirCURL($endpoint, $metodo, $data = null) {
    global $BASE_URL;
    $url = $BASE_URL . $endpoint;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    if ($data !== null) {
        $json = json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json)
        ]);
    }

    $respuesta = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        die("Error en la petición $metodo: " . curl_error($ch));
    }

    curl_close($ch);

    return ["codigo" => $http_code, "respuesta" => $respuesta];
}
?>