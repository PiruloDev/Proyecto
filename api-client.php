<?php
$url_clientes = "http://localhost:8080/detalle/cliente";
$url_admins   = "http://localhost:8080/detalle/administrador";
$url_crear_cliente = "http://localhost:8080/crear/cliente";

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

// GET //
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

// POST // 
echo ("Desea ingresar un nuevo usuario?\n");
echo "Seleccione el numero de opcion\n";
echo "1. Si \n";
echo "2. No \n";
$peticion = readline ();

if($peticion == 1) {
    $nombre = readline ("Ingrese el nombre: ");
    $telefono = readline ("Ingrese el telefono: ");
    $correo = readline ("Ingrese el correo: ");
    $contrasena = readline ("Ingrese la contraseña: ");

    $datos_nuevos = array(
        "nombre" => $nombre,
        "email" => $correo,
        "telefono" => $telefono,
        "contrasena" => $contrasena
    );

    $data_json = json_encode($datos_nuevos);
    
    $proceso = curl_init($url_crear_cliente);
    curl_setopt($proceso, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($proceso, CURLOPT_POSTFIELDS, $data_json);
    curl_setopt($proceso, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($proceso, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "Content-Length: " . strlen($data_json)
        ));

    $response = curl_exec($proceso);
    $http_code = curl_getinfo($proceso, CURLINFO_HTTP_CODE);

    if(curl_errno($proceso)) {
        echo 'Error en la petición: ' . curl_error($proceso);
    } else {
        if ($http_code == 200) {
            echo "Usuario creado exitosamente.\n";
        } else {
            echo "Error al crear el usuario. Código HTTP: $http_code\n";
        }
    }
    curl_close($proceso);
}
?>
