<?php

$urlpost = "http://localhost:8080/crearingrediente";
$url = "http://localhost:8080/ingredientes";

$consumo = file_get_contents($url);

if ($consumo === false) {
    die("Error al consumir el servicio.");
}

$Ingredientes = json_decode($consumo);

foreach ($Ingredientes as $ingrediente) {
    echo $ingrediente . "\n";
}

//Metodo post

$respuesta= readline("¿Deseas agregar un nuevo ingrediente?, s para si, n para no: ");

if($respuesta === "s"){

    $idProveedor = readline("Ingresa el ID del proveedor: ");
    $idCategoria = readline("Ingresa el ID de la categoría: ");
    $nombre = readline("Ingresa el nombre del ingrediente: ");
    $cantidad = readline("Ingresa la cantidad: ");
    $fechaVencimiento = readline("Ingresa la fecha de vencimiento (YYYY-MM-DD): ");
    $referencia = readline("Ingresa la referencia: ");
    $fechaEntrega = readline("Ingresa la fecha de entrega (YYYY-MM-DD): ");

    $data = json_encode([
        "idProveedor" => $idProveedor,
        "idCategoria" => $idCategoria,
        "nombreIngrediente" => $nombre,
        "cantidadIngrediente" => $cantidad,
        "fechaVencimiento" => $fechaVencimiento,
        "referenciaIngrediente" => $referencia,
        "fechaEntregaIngrediente" => $fechaEntrega
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
        echo "Error al agregar el ingrediente Código HTTP: $http_code \n";
    
}
}