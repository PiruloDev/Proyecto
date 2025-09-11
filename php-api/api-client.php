<?php
$url_clientes = "http://localhost:8080/detalle/cliente";
$url_admins   = "http://localhost:8080/detalle/administrador";

$consumo_clientes = file_get_contents($url_clientes);
$consumo_admins   = file_get_contents($url_admins);

if ($consumo_clientes === FALSE || $consumo_admins === FALSE) {
    die("Error al consumir uno de los servicios.");
}

$usuarios_clientes = json_decode($consumo_clientes, true);
$usuarios_admins   = json_decode($consumo_admins, true);

echo "=================================\n";
echo "---------- PAN&CODE -------------\n";
echo "=================================\n";

echo "Ingrese su nombre: ";
$nombre_user = readline();

echo "Seleccione el rol\n";
echo "1. Administrador\n";
echo "2. Empleado\n";
echo "3. Salir\n";
$decision = readline();

if ($decision == 1) {
    echo "Bienvenido Administrador $nombre_user\n";
    echo "¿Desea ver todos los datos de los clientes?\n";
    echo "1. Sí\n";
    echo "2. No\n";
    $opcion = readline();

    if ($opcion == 1) {
        foreach ($usuarios_clientes as $recorrido => $usuario) {
            echo "Cliente " . ($recorrido + 1) . ":\n";
            foreach ($usuario as $atributo => $valor) {
                echo "  $atributo $valor\n";
            }
            echo "------------------------\n";
        }
        echo "====================================================\n";
        echo "Total de clientes: " . count($usuarios_clientes) . "\n";
        echo "====================================================\n";
    } else if ($opcion == 2) {
        echo "Vista de clientes omitida.\n";
    }

} else if ($decision == 2) {
    echo "Bienvenido Empleado $nombre_user\n";
    echo "¿Desea ver todos los datos de los administradores?\n";
    echo "1. Sí\n";
    echo "2. No\n";
    $opcion = readline();

    if ($opcion == 1) {
        foreach ($usuarios_admins as $recorrido => $admin) {
            echo "Administrador " . ($recorrido + 1) . ":\n";
            echo "  Nombre: " . ($admin['Nombre:'] ?? 'No disponible') . "\n";
            echo "  Telefono: " . ($admin['Telefono:'] ?? 'No disponible') . "\n";
            echo "------------------------\n";
        }
        echo "====================================================\n";
        echo "Total de administradores: " . count($usuarios_admins) . "\n";
        echo "====================================================\n";
    } else if ($opcion == 2) {
        echo "Saliendo del sistema....\n";
    }

} else if ($decision == 3) {
    echo "Cerrando programa...\n";
    exit;
} else {
    echo "Opción inválida.\n";
}
?>
