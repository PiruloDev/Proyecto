<?php
require_once "config.php";

// --------- MÉTODO GET ----------
$ventas = consumirGET("/reporte/ventas");

if (empty($ventas)) {
    echo "No se pudieron obtener las ventas o no hay datos disponibles.\n";
    exit;
}

echo "ID Factura | ID Cliente | ID Pedido | Fecha Facturación | Total Factura\n";
echo "------------------------------------------------------------------------\n";

foreach ($ventas as $venta) {
    echo "$venta->idFactura | $venta->idCliente | $venta->idPedido | $venta->fechaFacturacion | $venta->totalFactura\n";
}

// --------- BÚSQUEDA DE FACTURA ----------
$idBuscar = readline("Ingrese el ID de la factura que desea ver en detalle: ");

echo "\n RESULTADO DE LA BÚSQUEDA \n";

$encontrado = false;
foreach ($ventas as $venta) {
    if ($venta->idFactura == $idBuscar) {
        echo "ID Factura: $venta->idFactura\n";
        echo "ID Cliente: $venta->idCliente\n";
        echo "ID Pedido: $venta->idPedido\n";
        echo "Fecha Facturación: $venta->fechaFacturacion\n";
        echo "Total Factura: $venta->totalFactura\n";
        $encontrado = true;
        break;
    }
}

if (!$encontrado) {
    echo "No se encontró ninguna factura con el ID ingresado.\n";
}

// --------- MÉTODO POST ----------
$respuesta = readline("¿Desea agregar una nueva venta? (s/n): ");

if (strtolower($respuesta) === "s") {
    $idCliente = readline("Ingrese el ID del cliente: ");
    $idPedido = readline("Ingrese el ID del pedido: ");
    $fecha = readline("Ingrese la fecha de facturación (YYYY-MM-DD): ");
    $hora = readline("Ingrese la hora de facturación (HH:MM:SS): ");    
    $totalFactura = readline("Ingrese el total de la factura: ");

     $fechaFacturacion = $fecha . "T" . $hora;

        $data = [
            "idCliente" => (int)$idCliente,
            "idPedido" => (int)$idPedido,
            "fechaFacturacion" => $fechaFacturacion,
            "totalFactura" => (float)$totalFactura
        ];

        $resultado = consumirCURL("/agregar/venta", "POST", $data);

        if ($resultado["codigo"] === 200) {
            echo "Venta guardada correctamente (200)\n";
            echo "Respuesta del servidor: {$resultado['respuesta']}\n";
        } else {
            echo "Error en el servidor, respuesta: {$resultado['codigo']}\n";
            echo "Detalle: {$resultado['respuesta']}\n";
        }
    }

// --------- MÉTODO PATCH ----------
$respuesta_patch = readline("¿Desea actualizar una venta? (s/n): ");

if (strtolower($respuesta_patch) === "s") {
    $id = readline("Ingrese el ID de la factura a actualizar: ");
    $idCliente = readline("Ingrese nuevo ID del cliente: ");
    $idPedido = readline("Ingrese nuevo ID del pedido: ");
    $fecha = readline("Ingrese nueva fecha de facturación (YYYY-MM-DD): ");
    $hora = readline("Ingrese la hora de facturación (HH:MM:SS): ");
    $totalFactura = readline("Ingrese nuevo total de la factura: ");

    $fechaFacturacion = $fecha . "T" . $hora;

        $data = [
            "idCliente" => (int)$idCliente,
            "idPedido" => (int)$idPedido,
            "fechaFacturacion" => $fechaFacturacion,
            "totalFactura" => (float)$totalFactura
        ];

        $resultado = consumirCURL("/actualizar/venta/$id", "PATCH", $data);

        if ($resultado["codigo"] === 200) {
            echo "Venta actualizada correctamente (200)\n";
            echo "Respuesta del servidor: {$resultado['respuesta']}\n";
        } else {
            echo "Error en el servidor. Código de respuesta: {$resultado['codigo']}\n";
            echo "Detalle: {$resultado['respuesta']}\n";
        }
    }

// --------- MÉTODO DELETE ----------
$respuesta_delete = readline("¿Desea eliminar una venta? (s/n): ");

if (strtolower($respuesta_delete) === "s") {
    $id = readline("Ingrese el ID de la factura a eliminar: ");

    if (empty($id)) {
        echo "Error: El ID es obligatorio.\n";
    } else {
        $resultado = consumirCURL("/eliminar/venta/$id", "DELETE");

        if ($resultado["codigo"] === 200) {
            echo "Venta eliminada correctamente (200)\n";
            echo "Respuesta del servidor: {$resultado['respuesta']}\n";
        } else {
            echo "Error en el servidor. Código de respuesta: {$resultado['codigo']}\n";
            echo "Detalle: {$resultado['respuesta']}\n";
        }
    }
}
?>
