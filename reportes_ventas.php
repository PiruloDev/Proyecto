<?php
$url = "http://localhost:8080/ventas";
$consumo = file_get_contents($url);
if ($consumo === FALSE) {
    die("Error al consumir el servicio. ");
}

$ventas = json_decode($consumo);

echo "ID Factura | ID Cliente | ID Pedido | Fecha Facturación | Total Factura\n";
echo "------------------------------------------------------------------------\n";


foreach ($ventas as $venta) {
    echo $venta->idFactura . " | " .
         $venta->idCliente . " | " .
         $venta->idPedido . " | " .
         $venta->fechaFacturacion . " | " .
         $venta->totalFactura . "\n";
}

$idBuscar = readline("Ingrese el ID de la factura que desea ver en detalle: ");

echo "\n RESULTADO DE LA BÚSQUEDA \n";
$encontrado = false;

foreach ($ventas as $venta) {
    if ($venta->idFactura == $idBuscar) {
        echo "ID Factura: " . $venta->idFactura . "\n";
        echo "ID Cliente: " . $venta->idCliente . "\n";
        echo "ID Pedido: " . $venta->idPedido . "\n";
        echo "Fecha Facturación: " . $venta->fechaFacturacion . "\n";
        echo "Total Factura: " . $venta->totalFactura . "\n";
        $encontrado = true;
    }
}
?>
