<?php
$url = "http://localhost:8080/clientes";
$consumo = file_get_contents($url);
if ($consumo === FALSE) {
    die("Error al consumir el servicio. ");
}

$clientes = json_decode($consumo);

echo " Nombre \n";
echo "---------\n";

foreach ($clientes as $cliente) {
    echo $cliente . "\n";
}

?>
