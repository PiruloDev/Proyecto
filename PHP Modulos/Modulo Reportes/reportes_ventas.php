<?php
require_once "config.php";

$ventas = consumirGET("/ventas");

echo "ID Factura | ID Cliente | ID Pedido | Fecha Facturación | Total Factura\n";
echo "------------------------------------------------------------------------\n";

foreach ($ventas as $venta) {
    echo "$venta->idFactura | $venta->idCliente | $venta->idPedido | $venta->fechaFacturacion | $venta->totalFactura\n";
}

$idBuscar = readline("Ingrese el ID de la factura que desea ver en detalle: ");

echo "\n RESULTADO DE LA BÚSQUEDA \n";

foreach ($ventas as $venta) {
    if ($venta->idFactura == $idBuscar) {
        echo "ID Factura: $venta->idFactura\n";
        echo "ID Cliente: $venta->idCliente\n";
        echo "ID Pedido: $venta->idPedido\n";
        echo "Fecha Facturación: $venta->fechaFacturacion\n";
        echo "Total Factura: $venta->totalFactura\n";
    }
}

// --------- MÉTODO POST ----------
$respuesta = readline("¿Desea agregar una nueva venta? (s/n): ");

if (strtolower($respuesta) === "s") {
    $idCliente = readline("Ingrese el ID del cliente: ");
    $idPedido = readline("Ingrese el ID del pedido: ");
    $fechaFacturacion = readline("Ingrese la fecha de facturación (YYYY-MM-DD): ");
    $totalFactura = readline("Ingrese el total de la factura: ");

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
    }
}

// --------- MÉTODO PATCH ----------
$respuesta_patch = readline("¿Desea actualizar una venta? (s/n): ");

if ($respuesta_patch === "s") {
    $id = readline("Ingrese el ID de la factura a actualizar: ");
    $idCliente = readline("Ingrese nuevo ID del cliente: ");
    $idPedido = readline("Ingrese nuevo ID del pedido: ");
    $idFechaFacturación = readline("Ingrese nueva fecha de facturación (YYYY-MM-DD): ");
    $idTotalFactura = readline("Ingrese nuevo total de la factura: ");

    $data = ["idCliente" => $idCliente];
    $data = ["idPedido" => $idPedido];
    $data = ["idFechaFacturacion" => $idFechaFacturación];
    $data = ["idTotalFactura" => $idTotalFactura];

    $resultado = consumirCURL("/actualizar/venta/$id", "PATCH", $data);

    if ($resultado["codigo"] === 200) {
        echo "Venta actualizada correctamente (200)\n";
        echo "Respuesta del servidor: {$resultado['respuesta']}\n";
    } else {
        echo "Error en el servidor. Código de respuesta: {$resultado['codigo']}\n";
    }
}
?>
