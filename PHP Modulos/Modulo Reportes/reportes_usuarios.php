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

?>