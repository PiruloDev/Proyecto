<?php
// Configuración de la API
define('API_BASE_URL', 'http://localhost:8080'); 

// Función para consumir endpoint GET
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
        echo "Error cURL: " . curl_error($curl) . "\n";
        curl_close($curl);
        return [];
    }
    
    curl_close($curl);
    
    if ($httpCode !== 200) {
        echo "Error HTTP: $httpCode\n";
        return [];
    }
    
    return json_decode($response);
}

// Función para consumir endpoints con cURL (POST, PATCH, DELETE)
function consumirCURL($endpoint, $method, $data = []) {
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
    } elseif ($method === 'PATCH') {
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif ($method === 'DELETE') {
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    if (curl_errno($curl)) {
        echo "Error cURL: " . curl_error($curl) . "\n";
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
?>