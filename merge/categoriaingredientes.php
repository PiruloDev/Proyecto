<?php

$url = "http://localhost:8080/categorias/ingredientes";
$urlpost = "http://localhost:8080/nuevacategoriaingrediente";

$consumo = file_get_contents($url);

if ($consumo === false) {
    die("Error al consumir el servicio.");
}

$Categorias = json_decode($consumo);

foreach ($Categorias as $categoria) {
    echo "idCategoriaIngrediente: " . $categoria->idCategoriaIngrediente . "\n";
    echo "nombreCategoria: " . $categoria->nombreCategoria . "\n";
    echo "---------------------------------". "\n";
}


//Metodo post

$respuesta= readline("¿Deseas agregar una nueva Categoria?, s para si, n para no: ");

if($respuesta === "s"){

    $nombreCategoria = readline("Ingresa el nombre de la categoria: ");

    $data = json_encode([
        "nombreCategoria" => $nombreCategoria
    ], JSON_UNESCAPED_UNICODE);

    $proceso = curl_init($urlpost);
    curl_setopt($proceso, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($proceso, CURLOPT_POSTFIELDS, $data);
    curl_setopt($proceso, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($proceso, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        // Línea corregida
        'Content-Length: ' . strlen($data),
    ));

    $respuestapet= curl_exec($proceso);
    $http_code= curl_getinfo($proceso, CURLINFO_HTTP_CODE);

    // Los bloques if siguientes estaban mal anidados, se corrigieron para estar dentro del if($respuesta === 's')
    if(curl_errno($proceso)){
        die("Error en la petición POST \n");
    }
    curl_close($proceso);

    if($http_code === 200){
        echo "Categoria agregada exitosamente \n";
    } else {
        echo "Error al agregar la Categoria del proveedor Código HTTP: $http_code \n";
    }
}
?>