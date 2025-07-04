<?php
$servidor = "localhost";
$usuario = "root";
$password = "";
$base_datos = "ProyectoPanaderia"; // Nota: con mayúsculas como en el SQL

// Conexión MySQLi
$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Establecer charset
$conexion->set_charset("utf8");

// Opcional: mantener compatibilidad con PDO para otros archivos que lo usen
try {
    $pdo_conexion = new PDO("mysql:host=$servidor;dbname=$base_datos;charset=utf8", $usuario, $password);
    $pdo_conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error de conexión PDO: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Usuarios</title>
    <link rel="stylesheet" href="usuariosregistrados.css">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <h1>Usuarios Registrados</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Telefono</th>
                <th>Cliente activo</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <form action="usuariosregistrados.php" method="post">
        <button type="submit">Descargar Reporte</button>
    </form>
</body>
</html>
<?php
            $sql = "SELECT * FROM Clientes";
            $resultado = $conexion->query($sql);

            while ($fila = $resultado->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" , $fila['ID_CLIENTE'] . "</td>";
                echo "<td>" , $fila['NOMBRE_CLI'] . "</td>";
                echo "<td>" . $fila['TELEFONO_CLI'] . "</td>";
                echo "<td>" . $fila['ACTIVO_CLI'] . "</td>";
                echo "<td>" . $fila['EMAIL_CLI'] . "</td>";
                echo "<td>" . $fila['CONTRASEÑA_CLI'] . "</td>";
                echo "<td>" . $fila['SALT_CLI'] . "</td>";
                echo "</tr>";
            }
            ?>