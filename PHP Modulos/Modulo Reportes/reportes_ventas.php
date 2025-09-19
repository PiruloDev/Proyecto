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

$idCliente = readline("Ingrese el ID del cliente: ");
$idPedido = readline("Ingrese el ID del pedido: ");
$fechaFacturacion = readline("Ingrese la fecha de facturación (YYYY-MM-DD): ");
$totalFactura = readline("Ingrese el total de la factura: ");

$nuevaVenta = [
    "idCliente" => (int)$idCliente,
    "idPedido" => (int)$idPedido,
    "fechaFacturacion" => $fechaFacturacion,
    "totalFactura" => (float)$totalFactura
];

$respuesta = consumirCURL("/agregar/venta", "POST", $nuevaVenta);

// Respuesta del servidor
echo "Código HTTP: " . $respuesta["codigo"] . "\n";
echo "Respuesta del servidor: " . $respuesta["respuesta"] . "\n";

// --------- MÉTODO PATCH ----------
$actualizar = readline("¿Desea actualizar una venta existente? (s/n): ");

if (strtolower($actualizar) === "s") {
    $idActualizar = readline("Ingrese el ID de la factura que desea actualizar: ");

    echo "\nIngrese los nuevos datos (deje en blanco si no quiere modificar ese campo)\n";

    $idCliente = readline("Nuevo ID del cliente: ");
    $idPedido = readline("Nuevo ID del pedido: ");
    $fechaFacturacion = readline("Nueva fecha de facturación (YYYY-MM-DD): ");
    $totalFactura = readline("Nuevo total de la factura: ");

    $datosActualizados = [];

    if ($idCliente !== "") $datosActualizados["idCliente"] = (int)$idCliente;
    if ($idPedido !== "") $datosActualizados["idPedido"] = (int)$idPedido;
    if ($fechaFacturacion !== "") $datosActualizados["fechaFacturacion"] = $fechaFacturacion;
    if ($totalFactura !== "") $datosActualizados["totalFactura"] = (float)$totalFactura;

    $respuesta = consumirCURL("/actualizar/venta/$idActualizar", "PATCH", $datosActualizados);

    echo "Código HTTP: " . $respuesta["codigo"] . "\n";
    echo "Respuesta del servidor: " . $respuesta["respuesta"] . "\n";
}
?>
