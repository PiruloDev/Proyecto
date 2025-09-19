<?php

require_once 'config/config.php';

/**
 * Función para realizar una llamada a la API usando cURL.
 * @param string 
 * @param string 
 * @param array 
 * @return array 
 */
function callApi($url, $method, $data = null) {
    $proceso = curl_init($url);
    curl_setopt($proceso, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($proceso, CURLOPT_RETURNTRANSFER, true);
    
    if ($data) {
        $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE);
        curl_setopt($proceso, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($proceso, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ]);
    }
    
    $respuestaPet = curl_exec($proceso);
    $httpCode = curl_getinfo($proceso, CURLINFO_HTTP_CODE);

    if (curl_errno($proceso)) {
        die("Error en la petición: " . curl_error($proceso) . "\n");
    }
    curl_close($proceso);

    return [
        'body' => $respuestaPet,
        'status' => $httpCode
    ];
}

/*
    Método GET - Obtener todos los proveedores.
*/
function getProveedores() {
    echo "--- Lista de Proveedores ---\n";
    $url = endpointGet::API_GET_PROVEEDORES;
    $respuesta = callApi($url, "GET");

    if ($respuesta['status'] == 200) {
        $proveedores = json_decode($respuesta['body']);
        if (empty($proveedores)) {
            echo "No hay proveedores disponibles.\n";
        } else {
            foreach ($proveedores as $proveedor) {
                echo "ID proveedor: " . $proveedor->idProveedor . "\n";
                echo "nombreProv: " . $proveedor->nombreProv . "\n";
                echo "telefono: " . $proveedor->telefonoProv . "\n";
                echo "activoProv: " . ($proveedor->activoProv ? 'Sí' : 'No') . "\n";
                echo "emailProv: " . $proveedor->emailProv . "\n";
                echo "direccionProv: " . ($proveedor->direccionProv ?? 'N/A') . "\n";
                echo "---------------------------------\n";
            }
        }
    } else {
        echo "Error: Código HTTP " . $respuesta['status'] . "\n";
        echo "Respuesta: " . $respuesta['body'] . "\n";
    }
}

/*
    Método POST - Crear un nuevo proveedor.
*/
function postProveedor() {
    $url = endpointPost::API_CREAR_PROVEEDOR;
    $nombreProv = readline("Ingresa el nombre del proveedor: ");
    $telefonoProv = readline("Ingresa el teléfono del proveedor: ");
    $activoProv = strtolower(readline("¿Está activo el proveedor? (true/false): ")) === 'true';
    $emailProv = readline("Ingresa el email del proveedor: ");
    $direccionProv = readline("Ingresa la dirección del proveedor (dejar en blanco para null): ");

    $data = [
        "nombreProv" => $nombreProv,
        "telefonoProv" => $telefonoProv,
        "activoProv" => $activoProv,
        "emailProv" => $emailProv,
        "direccionProv" => ($direccionProv === '') ? null : $direccionProv
    ];
    
    $respuesta = callApi($url, "POST", $data);

    if ($respuesta['status'] == 200) {
        echo "Proveedor agregado exitosamente.\n";
        echo "Respuesta del servidor: " . $respuesta['body'] . "\n";
    } else {
        echo "Error al agregar el proveedor. Código HTTP: " . $respuesta['status'] . "\n";
        echo "Respuesta del servidor: " . $respuesta['body'] . "\n";
    }
}

/*
    Método PUT - Actualizar un proveedor existente.
*/
function putProveedor() {
    $id = (int)readline("Ingresa el ID del proveedor a actualizar: ");
    $nombreNuevo = readline("Ingresa el nuevo nombre del proveedor: ");
    $telefonoNuevo = readline("Ingresa el nuevo teléfono del proveedor: ");
    $activoNuevo = strtolower(readline("¿Está activo el proveedor? (true/false): ")) === 'true';
    $emailNuevo = readline("Ingresa el nuevo email del proveedor: ");
    $direccionNuevo = readline("Ingresa la nueva dirección del proveedor (dejar en blanco para null): ");
    
    $url = endpointPut::proveedor($id);
    
    $data = [
        "idProveedor" => $id,
        "nombreProv" => $nombreNuevo,
        "telefonoProv" => $telefonoNuevo,
        "activoProv" => $activoNuevo,
        "emailProv" => $emailNuevo,
        "direccionProv" => ($direccionNuevo === '') ? null : $direccionNuevo
    ];

    $respuesta = callApi($url, "PUT", $data);

    if ($respuesta['status'] == 200) {
        echo "Proveedor con ID " . $id . " actualizado correctamente.\n";
        echo "Respuesta del servidor: " . $respuesta['body'] . "\n";
    } else {
        echo "Error al actualizar el proveedor. Código HTTP: " . $respuesta['status'] . "\n";
        echo "Respuesta del servidor: " . $respuesta['body'] . "\n";
    }
}

/*
    Método DELETE 
*/
function deleteProveedor() {
    $id = (int)readline("Ingresa el ID del proveedor a eliminar: ");
    $url = endpointDelete::proveedor($id);

    $respuesta = callApi($url, "DELETE");

    if ($respuesta['status'] == 200) {
        echo "Proveedor con ID " . $id . " eliminado correctamente.\n";
        echo "Respuesta del servidor: " . $respuesta['body'] . "\n";
    } else {
        echo "Error al eliminar el proveedor. Código HTTP: " . $respuesta['status'] . "\n";
        echo "Respuesta del servidor: " . $respuesta['body'] . "\n";
    }
}

// Bucle principal para el menú interactivo
while (true) {
    echo "\n--- Menú de Operaciones API para Proveedores ---\n";
    echo "1. Obtener todos los proveedores (GET)\n";
    echo "2. Crear un nuevo proveedor (POST)\n";
    echo "3. Actualizar un proveedor (PUT)\n";
    echo "4. Eliminar un proveedor (DELETE)\n";
    echo "0. Salir\n";

    $opcion = readline("Selecciona una opción: ");

    switch ($opcion) {
        case '1':
            getProveedores();
            break;
        case '2':
            postProveedor();
            break;
        case '3':
            putProveedor();
            break;
        case '4':
            deleteProveedor();
            break;
        case '0':
            echo "Saliendo del programa.\n";
            exit;
        default:
            echo "Opción inválida. Por favor, selecciona un número del 0 al 4.\n";
            break;
    }
}
?>
