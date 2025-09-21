<?php
require_once "config.php";

// --------- MÉTODO GET ----------
$clientes = consumirGET("/reporte/clientes");

if (empty($clientes)) {
    echo "No se pudieron obtener los clientes o no hay datos disponibles.\n";
    exit;
}

echo " Nombre \n";
echo "---------\n";

foreach ($clientes as $cliente) {
    echo $cliente . "\n";
}

// --------- MÉTODO POST ----------
$respuesta = readline("¿Desea agregar un nuevo cliente? (s/n): ");

if (strtolower($respuesta) === "s") {
    $nombre = readline("Ingrese el nombre: ");

    if (empty($nombre)) {
        echo "Error: El nombre es obligatorio.\n";
    } else {
        $data = ["nombre" => $nombre];
        $resultado = consumirCURL("/agregar/cliente", "POST", $data);

        if ($resultado["codigo"] === 200) {
            echo "Cliente guardado correctamente (200)\n";
            echo "Respuesta del servidor: {$resultado['respuesta']}\n";
        } else {
            echo "Error en el servidor, respuesta: {$resultado['codigo']}\n";
            echo "Detalle: {$resultado['respuesta']}\n";
        }
    }
}

// --------- MÉTODO PATCH ----------
$respuesta_patch = readline("¿Desea actualizar un cliente? (s/n): ");

if (strtolower($respuesta_patch) === "s") {
    $id = readline("Ingrese el ID del cliente a actualizar: ");
    $nombre = readline("Ingrese nuevo nombre: ");

    if (empty($id) || empty($nombre)) {
        echo "Error: El ID y el nombre son obligatorios.\n";
    } else {
        $data = ["nombre" => $nombre];
        $resultado = consumirCURL("/actualizar/cliente/$id", "PATCH", $data);

        if ($resultado["codigo"] === 200) {
            echo "Cliente actualizado correctamente (200)\n";
            echo "Respuesta del servidor: {$resultado['respuesta']}\n";
        } else {
            echo "Error en el servidor. Código de respuesta: {$resultado['codigo']}\n";
            echo "Detalle: {$resultado['respuesta']}\n";
        }
    }
}

// --------- MÉTODO DELETE ----------
$respuesta_delete = readline("¿Desea eliminar un cliente? (s/n): ");

if (strtolower($respuesta_delete) === "s") {
    $id = readline("Ingrese el ID del cliente a eliminar: ");

    if (empty($id)) {
        echo "Error: El ID es obligatorio.\n";
    } else {
        $resultado = consumirCURL("/eliminar/cliente/$id", "DELETE");

        if ($resultado["codigo"] === 200) {
            echo "Cliente eliminado correctamente (200)\n";
            echo "Respuesta del servidor: {$resultado['respuesta']}\n";
        } else {
            echo "Error en el servidor. Código de respuesta: {$resultado['codigo']}\n";
            echo "Detalle: {$resultado['respuesta']}\n";
        }
    }
}
?>