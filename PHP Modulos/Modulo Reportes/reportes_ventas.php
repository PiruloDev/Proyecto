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
?>
