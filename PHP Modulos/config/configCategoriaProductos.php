<?php
// Base URL de tu API en Spring Boot
define('API_BASE_URL', 'http://localhost:8080'); 

// 🔹 Función para consumir un GET
function consumirGET($endpoint) {
    $url = API_BASE_URL . $endpoint;

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
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

// 🔹 Función para consumir POST, PUT y DELETE
function consumirAPI($endpoint, $method, $data = []) {
    $url = API_BASE_URL . $endpoint;

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ]
    ]);

    if ($method === 'POST') {
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif ($method === 'PUT') {
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif ($method === 'DELETE') {
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if (curl_errno($curl)) {
        echo "Error cURL: " . curl_error($curl);
        curl_close($curl);
        return [
            'codigo' => 0,
            'respuesta' => 'Error de conexión'
        ];
    }

    curl_close($curl);

    return [
        'codigo' => $httpCode,
        'respuesta' => $response
    ];
}
