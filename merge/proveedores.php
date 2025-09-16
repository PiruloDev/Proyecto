<?php


$urlpost = "http://localhost:8080/proveedores";
$url = "http://localhost:8080/proveedores";

$consumo = file_get_contents($url);

if ($consumo === false) {
    die("Error al consumir el servicio.");
}

$Proveedores = json_decode($consumo);

foreach ($Proveedores as $proveedor) {
    echo "ID proveedor: " . $proveedor->idProveedor . "\n";
    echo "nombreProv: " . $proveedor->nombreProv . "\n";
    echo "telefono: " . $proveedor->telefonoProv . "\n";
    echo "acivoProv: " . $proveedor->activoProv . "\n";
    echo "emailProv: " . $proveedor->emailProv . "\n";
    echo "direccionProv: ".  $proveedor->direccionProv . "\n";
    echo "---------------------------------". "\n";

}

//Metodo post

$respuesta= readline("¿Deseas agregar un nuevo proveedor?, s para si, n para no: ");

if($respuesta === "s"){

$nombreProv = readline("Ingresa el nombre del proveedor: ");
$telefonoProv = readline("Ingresa el teléfono del proveedor: ");
$activoProv = readline("¿Está activo el proveedor? (true/false): ");
$emailProv = readline("Ingresa el email del proveedor: ");
$direccionProv = readline("Ingresa la dirección del proveedor (dejar en blanco para null): ");

$activoProv = filter_var($activoProv, FILTER_VALIDATE_BOOLEAN);
// direccióon por defecto nulo
$direccionProv = ($direccionProv === '') ? null : $direccionProv;



$data = json_encode([ 
    "nombreProv" => $nombreProv,
    "telefonoProv" => $telefonoProv,
    "activoProv" => $activoProv,
    "emailProv" => $emailProv,
    "direccionProv" => $direccionProv
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




?>
