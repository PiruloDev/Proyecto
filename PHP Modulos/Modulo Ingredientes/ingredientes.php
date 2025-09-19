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
    Método GET - Obtener todos los ingredientes.
*/
function getIngredientes() {
    echo "--- Lista de Ingredientes ---\n";
    $url = endpointGet::API_GET_INGREDIENTES_LISTA;
    $respuesta = callApi($url, "GET");

    if ($respuesta['status'] == 200) {
        $ingredientes = json_decode($respuesta['body']);
        if (empty($ingredientes)) {
            echo "No hay ingredientes disponibles.\n";
        } else {
            foreach ($ingredientes as $ingrediente) {
                echo "ID Ingrediente: " . ($ingrediente->idIngrediente ?? 'N/A') . "\n";
                echo "Nombre: " . ($ingrediente->nombreIngrediente ?? 'N/A') . "\n";
                echo "Cantidad: " . ($ingrediente->cantidadIngrediente ?? 'N/A') . "\n";
                echo "Unidad: N/A\n"; 
                echo "ID de Categoría: " . ($ingrediente->idCategoria ?? 'N/A') . "\n";
                echo "ID de Proveedor: " . ($ingrediente->idProveedor ?? 'N/A') . "\n";
                echo "---------------------------------\n";
            }
        }
    } else {
        echo "Error: Código HTTP " . $respuesta['status'] . "\n";
        echo "Respuesta: " . $respuesta['body'] . "\n";
    }
}

/*
    Método POST - Crear un nuevo ingrediente.
*/
function postIngrediente() {
    $url = endpointPost::API_CREAR_INGREDIENTE;
    $nombre = readline("Ingresa el nombre del ingrediente: ");
    $cantidad = (int)readline("Ingresa la cantidad: ");
    $idCategoria = (int)readline("Ingresa el ID de la categoría (ej. 1): ");
    $idProveedor = (int)readline("Ingresa el ID del proveedor: ");
    $referencia = readline("Ingresa la referencia del ingrediente: ");
    $fechaVencimiento = readline("Ingresa la fecha de vencimiento (YYYY-MM-DD): ");


    $data = [
        "nombreIngrediente" => $nombre,
        "cantidadIngrediente" => $cantidad,
        "idCategoria" => $idCategoria,
        "idProveedor" => $idProveedor,
        "referenciaIngrediente" => $referencia,
        "fechaVencimiento" => $fechaVencimiento
    ];
    
    $respuesta = callApi($url, "POST", $data);

    if ($respuesta['status'] == 200) {
        echo "Ingrediente agregado exitosamente.\n";
        echo "Respuesta del servidor: " . $respuesta['body'] . "\n";
    } else {
        echo "Error al agregar el ingrediente. Código HTTP: " . $respuesta['status'] . "\n";
        echo "Respuesta del servidor: " . $respuesta['body'] . "\n";
    }
}

/*
    Método PUT - Actualizar un ingrediente existente.
*/
function putIngrediente() {
    $id = (int)readline("Ingresa el ID del ingrediente a actualizar: ");
    $nombreNuevo = readline("Ingresa el nuevo nombre del ingrediente: ");
    $cantidadNuevo = (int)readline("Ingresa la nueva cantidad: ");
    $idCategoriaNuevo = (int)readline("Ingresa el nuevo ID de la categoría (ej. 1): ");
    $idProveedorNuevo = (int)readline("Ingresa el nuevo ID del proveedor: ");
    $referenciaNuevo = readline("Ingresa la nueva referencia del ingrediente: ");
    $fechaVencimientoNuevo = readline("Ingresa la nueva fecha de vencimiento (YYYY-MM-DD): ");

    $url = endpointPut::ingrediente($id);
    
    $data = [
        "idIngrediente" => $id,
        "nombreIngrediente" => $nombreNuevo,
        "cantidadIngrediente" => $cantidadNuevo,
        "idCategoria" => $idCategoriaNuevo,
        "idProveedor" => $idProveedorNuevo,
        "referenciaIngrediente" => $referenciaNuevo,
        "fechaVencimiento" => $fechaVencimientoNuevo
    ];

    $respuesta = callApi($url, "PUT", $data);

    if ($respuesta['status'] == 200) {
        echo "Ingrediente con ID " . $id . " actualizado correctamente.\n";
        echo "Respuesta del servidor: " . $respuesta['body'] . "\n";
    } else {
        echo "Error al actualizar el ingrediente. Código HTTP: " . $respuesta['status'] . "\n";
        echo "Respuesta del servidor: " . $respuesta['body'] . "\n";
    }
}

/*
    Método DELETE - Eliminar un ingrediente por su ID.
*/
function deleteIngrediente() {
    $id = (int)readline("Ingresa el ID del ingrediente a eliminar: ");
    $url = endpointDelete::ingrediente($id);

    $respuesta = callApi($url, "DELETE");

    if ($respuesta['status'] == 200) {
        echo "Ingrediente con ID " . $id . " eliminado correctamente.\n";
        echo "Respuesta del servidor: " . $respuesta['body'] . "\n";
    } else {
        echo "Error al eliminar el ingrediente. Código HTTP: " . $respuesta['status'] . "\n";
        echo "Respuesta del servidor: " . $respuesta['body'] . "\n";
    }
}

// Bucle principal para el menú interactivo
while (true) {
    echo "\n--- Menú de Operaciones API para Ingredientes ---\n";
    echo "1. Obtener todos los ingredientes (GET)\n";
    echo "2. Crear un nuevo ingrediente (POST)\n";
    echo "3. Actualizar un ingrediente (PUT)\n";
    echo "4. Eliminar un ingrediente (DELETE)\n";
    echo "0. Salir\n";

    $opcion = readline("Selecciona una opción: ");

    switch ($opcion) {
        case '1':
            getIngredientes();
            break;
        case '2':
            postIngrediente();
            break;
        case '3':
            putIngrediente();
            break;
        case '4':
            deleteIngrediente();
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
