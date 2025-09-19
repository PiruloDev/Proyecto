<?php

class endpointPedidos {
    const API_BASE_URL = "http://localhost:8080/pedido/proveedores";
    
    public static function pedido($id) {
        return self::API_BASE_URL . "/" . $id;
    }
}

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
    Método GET - Obtener todos los pedidos de proveedores.
*/
function getPedidos() {
    echo "--- Lista de Pedidos de Proveedores ---\n";
    $url = endpointPedidos::API_BASE_URL;
    $respuesta = callApi($url, "GET");

    if ($respuesta['status'] == 200) {
        $pedidos = json_decode($respuesta['body']);
        if (empty($pedidos)) {
            echo "No hay pedidos de proveedores disponibles.\n";
        } else {
            foreach ($pedidos as $pedido) {
                echo "ID de Pedido: " . ($pedido->idPedidoProv ?? 'N/A') . "\n";
                echo "Número de Pedido: " . ($pedido->numeroPedido ?? 'N/A') . "\n";
                echo "Estado: " . ($pedido->estadoPedido ?? 'N/A') . "\n";
                echo "Fecha: " . ($pedido->fechaPedido ?? 'N/A') . "\n";
                echo "ID de Proveedor: " . ($pedido->idProveedor ?? 'N/A') . "\n";
                echo "---------------------------------\n";
            }
        }
    } else {
        echo "Error: Código HTTP " . $respuesta['status'] . "\n";
        echo "Respuesta: " . $respuesta['body'] . "\n";
    }
}

/*
    Método POST - Crear un nuevo pedido.
*/
function postPedido() {
    $url = endpointPedidos::API_BASE_URL;
    $fechaPedido = readline("Ingresa la fecha del pedido (YYYY-MM-DD): ");
    $estadoPedido = readline("Ingresa el estado del pedido: ");
    $numeroPedido = readline("Ingresa el número de pedido: ");
    $idProveedor = (int)readline("Ingresa el ID del proveedor: ");
    $idPedidoProv = (int)readline("Ingresa el ID de pedido del proveedor: ");

    $data = [
        "fechaPedido" => $fechaPedido,
        "estadoPedido" => $estadoPedido,
        "numeroPedido" => (int)$numeroPedido,
        "idProveedor" => $idProveedor,
        "idPedidoProv" => $idPedidoProv
    ];
    
    $respuesta = callApi($url, "POST", $data);

    if ($respuesta['status'] == 200) {
        echo "Pedido agregado exitosamente.\n";
        echo "Respuesta del servidor: " . $respuesta['body'] . "\n";
    } else {
        echo "Error al agregar el pedido. Código HTTP: " . $respuesta['status'] . "\n";
        echo "Respuesta del servidor: " . $respuesta['body'] . "\n";
    }
}

/*
    Método PUT - Actualizar un pedido existente.
*/
function putPedido() {
    $id = (int)readline("Ingresa el ID del pedido a actualizar: ");
    $fechaNuevo = readline("Ingresa la nueva fecha del pedido (YYYY-MM-DD): ");
    $estadoNuevo = readline("Ingresa el nuevo estado del pedido: ");
    $numeroNuevo = (int)readline("Ingresa el nuevo número de pedido: ");
    $idProveedorNuevo = (int)readline("Ingresa el nuevo ID del proveedor: ");
    
    $url = endpointPedidos::pedido($id);
    
    $data = [
        "idPedidoProv" => $id,
        "fechaPedido" => $fechaNuevo,
        "estadoPedido" => $estadoNuevo,
        "numeroPedido" => $numeroNuevo,
        "idProveedor" => $idProveedorNuevo
    ];

    $respuesta = callApi($url, "PUT", $data);

    if ($respuesta['status'] == 200) {
        echo "Pedido con ID " . $id . " actualizado correctamente.\n";
        echo "Respuesta del servidor: " . $respuesta['body'] . "\n";
    } else {
        echo "Error al actualizar el pedido. Código HTTP: " . $respuesta['status'] . "\n";
        echo "Respuesta del servidor: " . $respuesta['body'] . "\n";
    }
}

/*
    Método DELETE - Eliminar un pedido por su ID.
*/
function deletePedido() {
    $id = (int)readline("Ingresa el ID del pedido a eliminar: ");
    $url = endpointPedidos::pedido($id);

    $respuesta = callApi($url, "DELETE");

    if ($respuesta['status'] == 200) {
        echo "Pedido con ID " . $id . " eliminado correctamente.\n";
        echo "Respuesta del servidor: " . $respuesta['body'] . "\n";
    } else {
        echo "Error al eliminar el pedido. Código HTTP: " . $respuesta['status'] . "\n";
        echo "Respuesta del servidor: " . $respuesta['body'] . "\n";
    }
}

// Bucle principal para el menú interactivo
while (true) {
    echo "\n--- Menú de Operaciones API para Pedidos ---\n";
    echo "1. Obtener todos los pedidos (GET)\n";
    echo "2. Crear un nuevo pedido (POST)\n";
    echo "3. Actualizar un pedido (PUT)\n";
    echo "4. Eliminar un pedido (DELETE)\n";
    echo "0. Salir\n";

    $opcion = readline("Selecciona una opción: ");

    switch ($opcion) {
        case '1':
            getPedidos();
            break;
        case '2':
            postPedido();
            break;
        case '3':
            putPedido();
            break;
        case '4':
            deletePedido();
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
