<?php

$urlpost = "http://localhost:8080/pedido/proveedores";
$url = "http://localhost:8080/pedido/proveedores";

$consumo = file_get_contents($url);

if ($consumo === false) {
    die("Error al consumir el servicio.");
}

$PedidoProv = json_decode($consumo);

foreach ($PedidoProv as $pedprov) {
    echo "fechaPedido: " . $pedprov->fechaPedido . "\n";
    echo "estadoPedido: " . $pedprov->estadoPedido . "\n";
    echo "numeroPedido: " . $pedprov->numeroPedido . "\n";
    echo "idProveedor: " . $pedprov->idProveedor . "\n";
    echo "idPedidoProv: " . $pedprov->idPedidoProv . "\n";
    echo "---------------------------------". "\n";

}

//Metodo post

$respuesta= readline("¿Deseas agregar un nuevo proveedor?, s para si, n para no: ");

if($respuesta === "s"){

$fechaPedido = readline("Ingresa la fecha del pedido (YYYY-MM-DD): ");
$estadoPedido = readline("Ingresa el estado del pedido: ");
$numeroPedido = readline("Ingresa el número de pedido: ");
$idProveedor = readline("Ingresa el ID del proveedor: ");
$idPedidoProv = readline("Ingresa el ID de pedido del proveedor: ");

$data = json_encode([
    "fechaPedido" => $fechaPedido,
    "estadoPedido" => $estadoPedido,
    "numeroPedido" => (int)$numeroPedido,
    "idProveedor" => (int)$idProveedor,
    "idPedidoProv" => (int)$idPedidoProv
], JSON_UNESCAPED_UNICODE);

    $proceso = curl_init($urlpost);
    curl_setopt($proceso, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($proceso, CURLOPT_POSTFIELDS, $data);
    curl_setopt($proceso, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($proceso, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Lenght:  . strlen($data_json)',
    ));

    $respuestapet= curl_exec($proceso);
    $http_code= curl_getinfo($proceso, CURLINFO_HTTP_CODE);

    if(curl_errno($proceso)){
        die("Error en la petición POST \n");
    }
    curl_close($proceso);

    if($http_code === 200){
        echo "Ingrediente agregado exitosamente \n";
    }
    else{
        echo "Error al agregar el pedido del proveedor Código HTTP: $http_code \n";
    
}
}




?>
