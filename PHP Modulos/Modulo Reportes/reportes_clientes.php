<?php
require_once "config.php";

// --------- MÉTODO GET ----------
$clientes = consumirGET("/clientes");

echo " Nombre \n";
echo "---------\n";

foreach ($clientes as $cliente) {
    echo $cliente . "\n";
}

// --------- MÉTODO POST ----------
$respuesta = readline("¿Desea agregar un nuevo cliente? (s/n): ");
if ($respuesta === "s") {
    $nombre = readline("Ingrese el nombre: ");

    $data = ["nombre" => $nombre];
    $resultado = consumirCURL("/agregar/cliente", "POST", $data);

    if ($resultado["codigo"] === 200) {
        echo "Cliente guardado correctamente (200)\n";
    } else {
        echo "Error en el servidor, respuesta: {$resultado['codigo']}\n";
    }
}

// --------- MÉTODO PATCH ----------
$respuesta_patch = readline("¿Desea actualizar un cliente? (s/n): ");
if ($respuesta_patch === "s") {
    $id = readline("Ingrese el ID del cliente a actualizar: ");
    $nombre = readline("Ingrese nuevo nombre: ");

    $data = ["nombre" => $nombre];
    $resultado = consumirCURL("/actualizar/cliente/$id", "PATCH", $data);

    if ($resultado["codigo"] === 200) {
        echo "Cliente actualizado correctamente (200)\n";
        echo "Respuesta del servidor: {$resultado['respuesta']}\n";
    } else {
        echo "Error en el servidor. Código de respuesta: {$resultado['codigo']}\n";
    }
}

// --------- MÉTODO DELETE ----------
$respuesta_delete = readline("¿Desea eliminar un cliente? (s/n): ");
if ($respuesta_delete === "s") {
    $id = readline("Ingrese el ID del cliente a eliminar: ");

    $resultado = consumirCURL("/eliminar/cliente/$id", "DELETE");

    if ($resultado["codigo"] === 200) {
        echo "Cliente eliminado correctamente (200)\n";
        echo "Respuesta del servidor: {$resultado['respuesta']}\n";
    } else {
        echo "Error en el servidor. Código de respuesta: {$resultado['codigo']}\n";
    }
}
?>