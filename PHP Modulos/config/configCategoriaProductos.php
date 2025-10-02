<?php
define('API_BASE_URL', 'http://localhost:8080');

class EndpointsCategorias {
    const LISTAR   = API_BASE_URL . '/categorias';
    const CREAR    = API_BASE_URL . '/categorias';

    public static function actualizar($id) {
        return API_BASE_URL . "/categorias/$id";
    }

    public static function eliminar($id) {
        return API_BASE_URL . "/categorias/$id";
    }
}

//GET
function consumirGET_Categorias($url) {
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Accept: application/json"
        ]
    ]);

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if (curl_errno($curl)) {
        echo "Error cURL: " . curl_error($curl);
        curl_close($curl);
        return [];
    }

    curl_close($curl);

    if ($httpCode !== 200) {
        echo "Error HTTP: $httpCode";
        return [];
    }

    return json_decode($response, true);
}

//POST, PUT y DELETE
function consumirAPI_Categorias($url, $method, $data = []) {
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Accept: application/json"
        ],
        CURLOPT_POSTFIELDS => !empty($data) ? json_encode($data) : null
    ]);

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if (curl_errno($curl)) {
        echo "Error cURL: " . curl_error($curl);
        curl_close($curl);
        return ["codigo" => 0, "respuesta" => "Error de conexión"];
    }

    curl_close($curl);

    return [
        "codigo" => $httpCode,
        "respuesta" => json_decode($response, true)
    ];
}
