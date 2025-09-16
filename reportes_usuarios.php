<?php
$url = "http://localhost:8080/reporte/usuarios";
$consumo = file_get_contents($url);
if ($consumo === FALSE) {
    die("Error al consumir el servicio. ");
}

$usuarios = json_decode($consumo);

echo "Nombre | Email | Telefono | Rol\n";
echo "----------------------------------\n";

foreach ($usuarios as $usuario) {
    echo $usuario->nombre . " | " .
         $usuario->email . " | " .
         $usuario->telefono . " | " .
         $usuario->rol . "\n";
}

$nombreBuscar = readline("Ingrese el nombre del usuario para ver detalles: ");

echo "\n RESULTADO DE LA BÚSQUEDA \n";
$encontrado = false;

foreach ($usuarios as $usuario) {
    if ($usuario->nombre == $nombreBuscar) {
        echo "nombre: " . $usuario->nombre . "\n";
        echo "email: " . $usuario->email . "\n";
        echo "telefono: " . $usuario->telefono . "\n";
        echo "rol: " . $usuario->rol . "\n";
        $encontrado = true;
    }
}

// Método POST
$respuesta = readline ("¿Desea agregar un nuevo reporte de usuario? Coloca s para (Si) n para (No)");
if ($respuesta === "s" ) {
    $nombre = readline ("Ingrese el nombre");

    $datos = array(
        $nombre
    );

    $data_json = json_encode ($datos);

    $proceso = curl_init($url);

    curl_setopt($proceso, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($proceso, CURLOPT_POSTFIELDS, $data_json);
    curl_setopt($proceso, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($proceso, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_json)
));

    $respuestapet = curl_exec($proceso);

    $http_code = curl_getinfo($proceso, CURLINFO_HTTP_CODE);
    
    if(curl_errno($proceso)) {
        die("Error en la petición Post". curl_error($proceso). "\n");
    }
    curl_close($proceso);

    if($http_code === 200) {
        echo ("Usuario guardado correctamente respuesta (200)". "\n");
        }else{
            echo ("Error en el servidor respuesta $http_code");
        }
}
?>