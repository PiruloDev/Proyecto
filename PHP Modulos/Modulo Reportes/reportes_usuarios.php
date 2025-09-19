<?php
require_once "config.php";

// --------- MÉTODO GET ----------
$usuarios = consumirGET("/reporte/usuarios");

echo "Nombre | Email | Telefono | Rol\n";
echo "----------------------------------\n";

foreach ($usuarios as $usuario) {
    echo "{$usuario->nombre} | {$usuario->email} | {$usuario->telefono} | {$usuario->rol}\n";
}

// --------- BÚSQUEDA DE USUARIO ----------
$nombreBuscar = readline("Ingrese el nombre del usuario para ver detalles: ");

echo "\n RESULTADO DE LA BÚSQUEDA \n";

$encontrado = false;
foreach ($usuarios as $usuario) {
    if ($usuario->nombre == $nombreBuscar) {
        echo "Nombre: {$usuario->nombre}\n";
        echo "Email: {$usuario->email}\n";
        echo "Teléfono: {$usuario->telefono}\n";
        echo "Rol: {$usuario->rol}\n";
        $encontrado = true;
    }
}

if (!$encontrado) {
    echo "No se encontró ningún usuario con el nombre ingresado.\n";
}
?>