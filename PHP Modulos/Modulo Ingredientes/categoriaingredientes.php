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


function getCategoriasIngredientes() {
    echo "--- Lista de Categorías de Ingredientes ---\n";
    $url = endpointGet::API_GET_CATEGORIAS;
    $respuesta = callApi($url, "GET");

    if ($respuesta['status'] == 200) {
        $categorias = json_decode($respuesta['body']);
        if (empty($categorias)) {
            echo "No hay categorías de ingredientes disponibles.\n";
        } else {
            foreach ($categorias as $categoria) {
                echo "idCategoriaIngrediente: " . $categoria->idCategoriaIngrediente . "\n";
                echo "nombreCategoria: " . $categoria->nombreCategoria . "\n";
                echo "---------------------------------\n";
            }
        }
    } else {
        echo "Error: Código HTTP " . $respuesta['status'] . "\n";
        echo "Respuesta: " . $respuesta['body'] . "\n";
    }
}

function postCategoriaIngrediente() {
    $url = endpointPost::API_CREAR_CATEGORIA;
    $nombreCategoria = readline("Ingresa el nombre de la nueva categoria: ");

    $data = [
        "nombreCategoria" => $nombreCategoria
    ];
    
    $respuesta = callApi($url, "POST", $data);

    if ($respuesta['status'] == 200) {
        echo "Categoria agregada exitosamente.\n";
        echo "Respuesta del servidor: " . $respuesta['body'] . "\n";
    } else {
        echo "Error al agregar la categoria. Código HTTP: " . $respuesta['status'] . "\n";
        echo "Respuesta del servidor: " . $respuesta['body'] . "\n";
    }
}

function putCategoriaIngrediente() {
    $id = (int)readline("Ingresa el ID de la categoria a actualizar: ");
    $nombreNuevo = readline("Ingresa el nuevo nombre para la categoria: ");
    
    $url = endpointPut::categoria($id);
    
    $data = [
        "idCategoriaIngrediente" => $id,
        "nombreCategoria" => $nombreNuevo
    ];

    $respuesta = callApi($url, "PUT", $data);

    if ($respuesta['status'] == 200) {
        echo "Categoria con ID " . $id . " actualizada correctamente.\n";
        echo "Respuesta del servidor: " . $respuesta['body'] . "\n";
    } else {
        echo "Error al actualizar la categoria. Código HTTP: " . $respuesta['status'] . "\n";
        echo "Respuesta del servidor: " . $respuesta['body'] . "\n";
    }
}


function deleteCategoriaIngrediente() {
    $id = (int)readline("Ingresa el ID de la categoria a eliminar: ");
    $url = endpointDelete::categoria($id);

    $respuesta = callApi($url, "DELETE");

    if ($respuesta['status'] == 200) {
        echo "Categoria con ID " . $id . " eliminada correctamente.\n";
        echo "Respuesta del servidor: " . $respuesta['body'] . "\n";
    } else {
        echo "Error al eliminar la categoria. Código HTTP: " . $respuesta['status'] . "\n";
        echo "Respuesta del servidor: " . $respuesta['body'] . "\n";
    }
}

while (true) {
    echo "\n--- Menú de Operaciones API para Categorías de Ingredientes ---\n";
    echo "1. Obtener todas las categorías (GET)\n";
    echo "2. Crear una nueva categoría (POST)\n";
    echo "3. Actualizar una categoría (PUT)\n";
    echo "4. Eliminar una categoría (DELETE)\n";
    echo "0. Salir\n";

    $opcion = readline("Selecciona una opción: ");

    switch ($opcion) {
        case '1':
            getCategoriasIngredientes();
            break;
        case '2':
            postCategoriaIngrediente();
            break;
        case '3':
            putCategoriaIngrediente();
            break;
        case '4':
            deleteCategoriaIngrediente();
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
